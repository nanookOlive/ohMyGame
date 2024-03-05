<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Bibliogame;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ProfilActionVoter extends Voter
{
    public const RETURN = "BIBLIOGAME_RETURN";
    public const ACCEPT = 'REQUEST_ACCEPT';
    public const DENY = 'REQUEST_DENY';


    protected function supports(string $action, mixed $bibliogame): bool
    {

        return in_array($action, [self::RETURN, self::ACCEPT, self::DENY])
            && $bibliogame instanceof Bibliogame;
    }

    protected function voteOnAttribute(string $action, mixed $bibliogame, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {


            return false;
        }


        switch ($action) {
            case self::RETURN:
                return $user === $bibliogame->getMember();
                break;

            case self::ACCEPT:
                return $user === $bibliogame->getMember();

                break;

            case self::DENY:
                return $user === $bibliogame->getMember();

                break;
        }

        // return false;
    }
}
