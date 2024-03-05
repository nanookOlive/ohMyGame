<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Game;
use App\Repository\EditorRepository;
use App\Repository\ThemeRepository;
use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class GameFixture
{
    public function __construct(
        private EditorRepository $editorRepository,
        private SluggerInterface $slugger,
        private EntityManagerInterface $em,
        private TypeRepository $typeRepository,
        private ThemeRepository $themeRepository
    ) {
    }

    public function createGame(int $qty)
    {
        $types = $this->typeRepository->findAll();
        $themes = $this->themeRepository->findAll();

        $faker = Factory::create();

        $editors = $this->editorRepository->findAll();
        for ($a = 0; $a < $qty; $a++) {
            $game = new Game;
            //title
            $title = $faker->words()[0];
            $game->setTitle($title);
            //editor
            $game->setEditor($editors[array_rand($editors)]);
            //age
            $game->setMinimumAge($faker->numberBetween(3, 99));
            //date de sortie
            $game->setReleasedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeThisCentury()));
            //note
            $game->setRating($faker->randomFloat(1, 0, 5));
            //duree
            $game->setDuration($faker->numberBetween(15, 120));
            //url_image
            // $game->setImageUrl('https://cdn.thewirecutter.com/wp-content/media/2020/10/smallworldboardgames-2048px-33.jpg');
            //joueurmin
            $game->setPlayersMin($faker->numberBetween(2, 4));
            //joueurmax
            $game->setPlayersMax($faker->numberBetween(4, 7));
            //slug
            $game->setSlug($this->slugger->slug($title));
            //descritpion courte
            $game->setShortDescription($faker->text(100));
            //descritpiton longue 
            $game->setLongDescription($faker->text(500));

            // types
            for ($i = 0; $i < 2; $i++) {
                $game->addType($types[rand(0, count($types) - 1)]);
            }
            // themes
            for ($i = 0; $i < 3; $i++) {
                $game->addTheme($themes[rand(0, count($themes) - 1)]);
            }

            $this->em->persist($game);
        }
    }
}
