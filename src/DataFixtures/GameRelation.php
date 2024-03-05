<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Type;
use App\Entity\Theme;
use App\Entity\Author;
use App\Entity\Editor;
use App\Entity\Illustrator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class GameRelation
{
    public function __construct(
        private UserPasswordHasherInterface $hasher,
        private EntityManagerInterface $em,
        private SluggerInterface $slugger
    ) {
    }

    /**
     * Création Editor
     *
     * @param integer $qty
     * @return void
     */
    public function createEditor(int $qty)
    {
        $faker = Factory::create();

        for ($i = 0; $i < $qty; $i++) {
            $name = $faker->word();
            $editor = new Editor();
            $editor->setName($name);
            $editor->setSlug($this->slugger->slug($name));

            $this->em->persist($editor);
        }
    }

    /**
     * Création Theme
     *
     * @param integer $qty
     * @return void
     */
    public function createTheme(int $qty)
    {
        $faker = Factory::create();

        for ($i = 0; $i < $qty; $i++) {
            $name = $faker->word();
            $theme = new Theme();
            $theme->setName($name);
            $theme->setSlug($this->slugger->slug($name));

            $this->em->persist($theme);
        }
    }

    /**
     * Création Type
     *
     * @param integer $qty
     * @return void
     */
    public function createType(int $qty)
    {
        $faker = Factory::create();

        for ($i = 0; $i < $qty; $i++) {
            $name = $faker->word();
            $type = new Type();
            $type->setName($name);
            $type->setSlug($this->slugger->slug($name));

            $this->em->persist($type);
        }
    }

    /**
     * Création Illustrator
     *
     * @param integer $qty
     * @return void
     */
    public function createIllustrator(int $qty)
    {

        $faker = Factory::create();

        for ($i = 0; $i < $qty; $i++) {
            $name = $faker->name();
            $illustrator = new Illustrator();
            $illustrator->setName($name);

            $this->em->persist($illustrator);
        }
    }

    /**
     * Création Author
     *
     * @param integer $qty
     * @return void
     */
    public function createAuthor(int $qty)
    {
        $faker = Factory::create();

        for ($i = 0; $i < $qty; $i++) {
            $name = $faker->name();
            $author = new Author();
            $author->setName($name);

            $this->em->persist($author);
        }
    }
}
