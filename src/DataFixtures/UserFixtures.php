<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures
{
    public function __construct(
        private UserPasswordHasherInterface $hasher,
        private EntityManagerInterface $em
    ) {
    }

    private $arrayAddress = [
        '1 rue Ameline', //Origine
        '23 Rue Emile Souvestre', //TRUE ouest
        '19 Rue Louis Primault', //TRUE est
        '111 Boulevard Michelet', //TRUE nord
        '1 Rue de Bel air', //TRUE sud
        '24 Rue Recteur Schmitt', //FALSE nord
        '16 rue de Rieux', //FALSE sud
        '134 Boulevard des Anglais', //FALSE ouest
        '66 Bd des Poilus', //FALSE est
    ];

    /**
     * Create Admin User
     *
     * @return void
     */
    public function createAdmin()
    {
        $faker = Factory::create('FR_fr');

        $admin = new User;
        $admin->setEmail('admin@ohmygame.fr')
            ->setPassword($this->hasher->hashPassword($admin, 'admin'))
            ->setRoles(['ROLE_ADMIN'])
            ->setFirstname($faker->firstName())
            ->setLastname($faker->lastName())
            ->setAlias($faker->firstName())
            ->setBirthAt(\DateTimeImmutable::createFromMutable($faker->dateTimeThisCentury()))
            ->setAddress($this->arrayAddress[0])
            ->setPostalCode(rand(01, 99) . '000')
            ->setCity('Nantes');
        // ->setAvatar('ohmygame_logo.png');
        // ->setLatitude(rand(448554, 464767) / 10000)
        // ->setLongitude(rand(9266, 35320) / 10000);

        $this->em->persist($admin);
    }

    /**
     * Create Users
     *
     * @param integer $qty
     * @return void
     */
    public function createUser(int $qty)
    {
        $faker = Factory::create('FR_fr');

        for ($i = 0; $i < $qty; $i++) {
            $user = new User;
            $user->setEmail('user' . $i . '@ohmygame.fr')
                ->setPassword($this->hasher->hashPassword($user, 'user'))
                ->setRoles([])
                ->setFirstname($faker->firstName())
                ->setLastname($faker->lastName())
                ->setAlias($faker->firstName())
                ->setBirthAt(\DateTimeImmutable::createFromMutable($faker->dateTimeThisCentury()))
                ->setAddress($this->arrayAddress[$i + 1])
                ->setPostalCode(rand(01, 99) . '000')
                ->setCity('Nantes');
            // ->setAvatar('ohmygame_logo.png');

            // ->setLatitude(rand(421806, 510893) / 10000)
            // ->setLongitude(rand(-56170, 81154) / 10000);

            $this->em->persist($user);
        }
    }
}
