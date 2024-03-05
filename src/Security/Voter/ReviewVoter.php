<?php

namespace App\Security\Voter;

use App\Entity\Game;
use App\Entity\Review;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ReviewVoter extends Voter
{
    public const POST = 'POST';
    public const DELETE = 'DELETE';

    protected function supports(string $action, mixed $game): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($action, [self::POST, self::DELETE])
            && $game instanceof Game;
    }

    protected function voteOnAttribute(string $action, mixed $game, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($action) {
            case self::POST:
                $reviews = $game->getReviews();
                foreach ($reviews as $review) {
                    if ($review->getMember() === $user) {
                        return false;  
                    }
                    }
                    break;
                    
            case self::DELETE:
                $reviews = $game->getReviews();
                foreach ($reviews as $review) {
                    if ($review->getMember() === $user) {
                        return true;
                    }
                    }
                    return false;

                }

                return true;
    }
}
