<?php

namespace App\EventListener;

use App\Entity\Game;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Game::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: Game::class)]
class GameListener
{
    public function __construct(
        private SluggerInterface $slugger
    ) {
    }

    /**
     * Créer le slug d'un jeu à sa création
     *
     * @param Game $game
     * @param PrePersistEventArgs $event
     * @return void
     */
    public function prePersist(Game $game, PrePersistEventArgs $event): void
    {
        $slug = strtolower($this->slugger->slug($game->getTitle()));
        //slug = str_replace('/','-',$game->getTitle());  
        //pour bien faire il faudrait remplacer tous les caractères spéciaux d'une regex qui pourraient se trouver dans le 
        //title d'un jeu 

        $game->setSlug($slug);
    }

    /**
     * Créer le slug d'un jeu à sa modification
     *
     * @param Game $game
     * @param PrePersistEventArgs $event
     * @return void
     */
    public function preUpdate(Game $game, PreUpdateEventArgs $event): void
    {
        $slug = strtolower($this->slugger->slug($game->getTitle()));
        //slug = str_replace('/','-',$game->getTitle());

        $game->setSlug($slug);
    }
}
