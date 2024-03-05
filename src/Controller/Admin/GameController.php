<?php

namespace App\Controller\Admin;

use App\Entity\Game;
use App\Form\GameType;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/game')]
class GameController extends AbstractController
{
    #[Route('/page/{page}', name: 'app_admin_game_index', methods: ['GET'])]
    public function index(int $page, GameRepository $gameRepository): Response
    {
        $paginator = $gameRepository->findByAdminPage($page - 1);

        return $this->render('admin/game/index.html.twig', [
            // 'games' => $gameRepository->findAll(),
            'nbPages' => count($paginator) / 5,
            'thisPage' => $page,
            'paginator' => $paginator
        ]);
    }

    #[Route('/new', name: 'app_admin_game_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $game = new Game();
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($game);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_game_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/game/new.html.twig', [
            'game' => $game,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_game_show', methods: ['GET'])]
    public function show(Game $game): Response
    {
        return $this->render('admin/game/show.html.twig', [
            'game' => $game,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_game_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Game $game, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_game_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/game/edit.html.twig', [
            'game' => $game,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_game_delete', methods: ['POST'])]
    public function delete(Request $request, Game $game, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $game->getId(), $request->request->get('_token'))) {
            $entityManager->remove($game);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_game_index', [], Response::HTTP_SEE_OTHER);
    }
}
