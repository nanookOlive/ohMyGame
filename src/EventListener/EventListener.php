<?php

namespace App\EventListener;

use App\Entity\Event;
use Doctrine\ORM\Events;
use App\Service\CoordinatesService;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Event::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: Event::class)]
class EventListener
{
    public function __construct(
        private CoordinatesService $coordinatesService
    ) {
    }

    /**
     * Enregistre les coordonnés GPS de Event, à PrePersist
     *
     * @param Event $event
     * @param PrePersistEventArgs $e
     * @return void
     */
    public function prePersist(Event $event, PrePersistEventArgs $e): void
    {
        $address = $event->getAddress();
        $coordinates = $this->coordinatesService->getCoordinates($address);

        if ($coordinates) {
            $event->setLatitude($coordinates['latitude'])
                ->setLongitude($coordinates['longitude']);
        }
    }

    /**
     * Enregistre les coordonnés GPS de Event, à PreUpdate
     *
     * @param Event $event
     * @param PreUpdateEventArgs $e
     * @return void
     */
    public function preUpdate(Event $event, PreUpdateEventArgs $e): void
    {
        $address = $event->getAddress();
        $coordinates = $this->coordinatesService->getCoordinates($address);

        if ($coordinates) {
            $event->setLatitude($coordinates['latitude'])
                ->setLongitude($coordinates['longitude']);
        }
    }
}
