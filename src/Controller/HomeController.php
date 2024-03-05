<?php

namespace App\Controller;

use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(GameRepository $gameRepository): Response
    {
        return $this->render('home/home.html.twig', [
            'randomGames' => $gameRepository->findRandomGames(4)
        ]);
    }
}
