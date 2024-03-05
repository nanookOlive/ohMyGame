<?php

namespace App\Controller;

use App\Entity\Game;
use App\Form\GameSearchBarType;
use App\Repository\GameRepository;
use App\Repository\TypeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GameController extends AbstractController
{
    public function __construct(
        private GameRepository $gameRepository
    ) {
    }

    /**
     * Page présentant les jeux de la base
     * Utilisation de la recherche
     */
    #[Route('/jeux', name: 'app_games_list')]
    public function index(Request $request): Response
    {
        $game = new Game;
        $form = $this->createForm(GameSearchBarType::class, $game);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $games = $this->gameRepository->findBySearchFields(
                $game->getTitle(),
                $game->getType(),
                $game->getTheme(),
                $game->getEditor(),
                $game->getMinimumAge(),
                $game->getReleasedAt()
            );
        } else {
            $games = $this->gameRepository->findBy([], ['title' => 'ASC'], 24);
        }

        return $this->render('game/list.html.twig', [
            'games' => $games,
            'form' => $form
        ]);
    }

    /**
     * Page présentant le détail d'un jeu
     */
    #[Route('/jeux/{slug}', name: 'app_games_details')]
    public function details(string $slug, TypeRepository $typeRepository): Response
    {
        $game = $this->gameRepository->findOneBySlug($slug);
        // $otherGames = $this->gameRepository->findBy(['editor' => $game->getEditor()], [], 4);
        return $this->render('game/details.html.twig', [
            'game' => $game,
            // 'otherGames' => $otherGames,
            'randomGames' => $this->gameRepository->findRandomGames(4)
        ]);
    }
}
