<?php

namespace App\DataFixtures;

use App\DataFixtures\UserFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function __construct(
        private GameRelation        $gameRelation,
        private UserFixtures        $userFixtures,
        private GameFixture         $gameFixture,
        private BibliogameFixtures  $bibliogameFixtures,
        private EventFixtures       $eventFixtures
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        /**
         * Run fixtures you want
         */
        // $this->gameRelation->createAuthor(5);
        // $this->gameRelation->createEditor(5);
        // $this->gameRelation->createIllustrator(5);
        // $this->gameRelation->createTheme(5);
        // $this->gameRelation->createType(5);

        $this->userFixtures->createAdmin();
        $this->userFixtures->createUser(8);

        $manager->flush();

        //$this->gameFixture->createGame(10);

       //$manager->flush();

        //$this->bibliogameFixtures->createBibliogames();

        //$manager->flush();

       // $this->eventFixtures->createEvent();

        //$manager->flush();
    }
}
