<?php

namespace App\DataFixtures;

use App\Entity\Event;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;

class EventFixtures 
{
    public function __construct(
        private UserRepository $userRepository,
        private GameRepository $gameRepository,
        private EntityManagerInterface $em
    ) {
    }

    /**
     * create Event
     *
     */
    public function createEvent()
    {
        $faker = Factory::create('fr_FR');

        // Récupérer les utilisateurs (host) depuis la base de données
        $users = $this->userRepository->findAll();

        for ($i = 1; $i <= 20; $i++) {
            $event = new Event();
            $event
                ->setTitle($faker->text(50))
                ->setDescription($faker->paragraph)
                //->setPicture("images/header_$i.jpg")
                ->setPlayersMin($faker->numberBetween(2, 5))
                ->setPlayersMax($faker->numberBetween(6, 20))
                ->setAddress($faker->address)
                ->setLatitude($faker->latitude)
                ->setLongitude($faker->longitude)
                ->setIsPublic($faker->boolean)
                ->setStartAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('now', '+1 week')))
                ->setEndAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('+1 week', '+2 week')));

            // Sélectionner un utilisateur aléatoire comme hôte (host) de l'événement
            $host = $faker->randomElement($users);
            $event->setHost($host);

            $this->em->persist($event);
        }
    }

}

