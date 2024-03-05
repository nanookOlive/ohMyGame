<?php

namespace App\Controller\Api;

use App\Entity\Bibliogame;
use App\Repository\BibliogameRepository;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BibliogameController extends AbstractController
{
    public function __construct(
        private BibliogameRepository $bibliogameRepository,
        private EntityManagerInterface $em,
        private GameRepository $gameRepository
    ) {
    }

    #[Route('/api/bibliogame', name: 'app_api_bibliogame', methods: ['GET'])]
    public function bibliogame(): JsonResponse
    {
        $bibliogames = $this->bibliogameRepository->findBy(['member' => $this->getUser()]);

        $array = [];
        foreach ($bibliogames as $b) {
            $array[] = [
                'html' => $this->render('game_card/_bibliogame_game_card.html.twig', [
                    'game' => $b->getGame(),
                    'bibliogame' => $b
                ])
            ];
        }

        return $this->json($array, Response::HTTP_OK, [], []);
    }

    /**
     * Add a game to my bibliogame with ajax
     *
     * @param Request $request
     * @return Response
     */
    #[Route('/api/bibliogame/action', name: 'app_api_bibliogame_action', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $jsonContent = json_decode($request->getContent(), true);
        $id = $jsonContent['id'];

        $b = $this->bibliogameRepository->findBy(['game' => $id, 'member' => $this->getUser()]);

        if ($b) {
            // Remove game from Biliogame
            $this->em->remove($b[0]);
            $this->em->flush();

            return $this->json(null, Response::HTTP_OK, [], []);
        } else {
            // Create a new Bibliogame
            $b = new Bibliogame;
            $b->setGame($this->gameRepository->find($id))
                ->setMember($this->getUser());

            $this->em->persist($b);
            $this->em->flush();

            return $this->json($b->getId(), Response::HTTP_CREATED, [], []);
        }
    }

    #[Route('/api/bibliogame/available/{id}', name: 'app_api_bibliogame_available', methods: ['GET'])]
    public function available(Bibliogame $bibliogame): JsonResponse
    {
        $status = $bibliogame->isIsAvailable();

        if ($status === true) {
            $bibliogame->setIsAvailable(false);
        } else {
            $bibliogame->setIsAvailable(true);
        }
        $this->em->flush();

        $status = $bibliogame->isIsAvailable();

        return $this->json($status, Response::HTTP_OK, [], []);
    }
}
