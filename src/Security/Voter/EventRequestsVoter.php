<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class EventRequestsVoter extends Voter
{
    public const STATUS = 'EVENT_REQUEST_STATUS';
    public const DELETE = 'EVENT_REQUEST_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::STATUS, self::DELETE])
            && $subject instanceof \App\Entity\EventRequests;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::STATUS:
                if ($user === $subject->getEvent()->getHost()) return true;
                break;

            case self::DELETE:
                if (
                    ($user === $subject->getEvent()->getHost()) ||
                    ($user === $subject->getUser())
                ) return true;
                break;
        }

        return false;
    }
}
