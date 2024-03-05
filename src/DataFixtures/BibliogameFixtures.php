<?php

namespace App\DataFixtures;

use Faker\Factory;
use DateTimeImmutable;
use App\Entity\Bibliogame;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class BibliogameFixtures
{
    public function __construct(
        private UserRepository $userRepository,
        private GameRepository $gameRepository,
        private EntityManagerInterface $em
    ) {
    }

    /**
     * Create Bibliogames
     *
     * @return void
     */
    public function createBibliogames()
    {
        $faker = Factory::create('FR_fr');

        $users = $this->userRepository->findAll();
        $games = $this->gameRepository->findAll();

        foreach ($users as $user) {

            for ($i = 0; $i < rand(2, 4); $i++) {

                $b = new Bibliogame;
                $b->setGame($games[rand(0, count($games) - 1)])
                    ->setMember($user)
                    ->setIsAvailable(random_int(0, 1));

                if ($b->isIsAvailable() == 1) {

                    if (random_int(0, 1)) {
                        $canBorrow = array_diff($users, [$user]);
                        $borrowedBy = array_rand($canBorrow);
                        $b->setBorrowedAt(DateTimeImmutable::createFromMutable($faker->dateTimeThisDecade()))
                            ->setBorrowedBy($canBorrow[$borrowedBy]);
                    }
                }

                $this->em->persist($b);
            }
        }
    }
}
