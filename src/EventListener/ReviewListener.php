<?php

namespace App\EventListener;

use App\Entity\Review;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Review::class)]
class ReviewListener
{

    public function __construct(
        private EntityManagerInterface $entityManager
    ){}
    /**
     * Récupérer la note d'un jeu pour le mettre à jour
     *
     * @param Review $review
     * @param PrePersistEventArgs $event
     * @return void
     */
    public function postPersist(Review $review, PostPersistEventArgs $event): void
    {
        // je récupère le jeu
        $game = $review->getGame();

        // je stock le total des notes
        $total = null;

        // je boucle sur les avis du jeu
        foreach($game->getReviews() as $review){
            $total += $review->getRating();
        }

        // je divise le total par le nombre d'avis
        $rating = $total / count($game->getReviews());

        // j'affecte la valeur au jeu
        $game->setRating(round($rating, 1));

        $this->entityManager->flush();
    }
}