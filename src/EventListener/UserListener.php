<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\Events;
use App\Service\CoordinatesService;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;

/**
 * Doc : https://adresse.data.gouv.fr/api-doc/adresse
 */
#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: User::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: User::class)]
class UserListener
{
    public function __construct(
        private CoordinatesService $coordinatesService
    ) {
    }

    /**
     * Enregistre les coordonnés GPS de User, à PrePersist
     *
     * @param User $user
     * @param PrePersistEventArgs $event
     * @return void
     */
    public function prePersist(User $user, PrePersistEventArgs $event): void
    {
        $address = $user->getAddress() . ' ' . $user->getCity();
        $coordinates = $this->coordinatesService->getCoordinates($address);

        if ($coordinates) {
            $user->setLatitude($coordinates['latitude'])
                ->setLongitude($coordinates['longitude']);
        }
    }

    /**
     * Enregistre les coordonnés GPS de User, à PreUpdate
     *
     * @param User $user
     * @param PreUpdateEventArgs $event
     * @return void
     */
    public function preUpdate(User $user, PreUpdateEventArgs $event): void
    {
        $address = $user->getAddress() . ' ' . $user->getCity();
        $coordinates = $this->coordinatesService->getCoordinates($address);

        if ($coordinates) {
            $user->setLatitude($coordinates['latitude'])
                ->setLongitude($coordinates['longitude']);
        }
    }
}
