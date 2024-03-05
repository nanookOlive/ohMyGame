<?php

namespace App\Controller\Api;

use App\Entity\Game;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    public function __construct(
        private Security $security
    ) {
    }

    /**
     * retourne l'utilisateur courant
     *
     * @return JsonResponse
     */
    #[Route('/api/user', name: 'app_api_user', methods: ["GET"])]
    public function user(): JsonResponse
    {
        /** @var User */
        $user = $this->getUser() ?: null;

        return $this->json($user, Response::HTTP_OK, [], ["groups" => "users"]);
    }

    /**
     * retourne la liste de tous les users au format json
     *
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    #[Route('/api/users', name: 'app_api_users', methods: ["GET"])]
    public function users(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();
        return $this->json($users, Response::HTTP_OK, [], ["groups" => "users"]);
    }

    /**
     * Retourne la lise de tous les users pour un jeu donné, au format json
     *
     * @param Game $game
     * @return JsonResponse
     */
    #[Route('/api/users/game/{id<\d+>}', name: 'app_api_users_game', methods: ["GET"])]
    public function usersByGame(Game $game): JsonResponse
    {
        $users = $game->getUsers();
        return $this->json($users, Response::HTTP_OK, [], ["groups" => "users"]);
    }
}
