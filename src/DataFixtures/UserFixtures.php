<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use PhpParser\Node\Expr\Array_;

class UserFixtures extends Fixture
{
    private Generator $faker;
    public function __construct(){
        $faker = Factory::create('fr_FR');
        $this->faker = $faker;
    }

    public function load(ObjectManager $manager): void
    {
         $user = new User();
        $user->setLastname($this->faker->lastName());
        $user->setFirstname($this->faker->firstNameMale());
        $user->setAddress($this->faker->address());
        $user->setZipcode($this->faker->postcode());
        $user->setCity($this->faker->city());
        $user->setEmail($this->faker->email());
        $user->setNumber($this->faker->phoneNumber());
        $user->setPseudo($this->faker->firstNameMale());
        $user->setPassword($this->faker->password());
        $user->setActive(True);
        $user->setRoles(['ROLE_USER']);
        $user->setSlug($this->faker->slug());
        $manager->persist($user);
        $manager->flush();
    }
}
