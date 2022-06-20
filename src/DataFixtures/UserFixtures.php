<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use PhpParser\Node\Expr\Array_;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserFixtures extends Fixture
{
    private Generator $faker;
    public function __construct(private SluggerInterface $slugger){
        $faker = Factory::create('fr_FR');
        $this->faker = $faker;
    }

    public function load(ObjectManager $manager): void
    {
        $count = $this->faker->numberBetween(10, 20);
        for($i = 1; $i <= $count; $i++){
            $this->createUser($manager);
        }
        $manager->flush();
    }

    public function createUser(ObjectManager $manager): User
    {
        $lastname = $this->faker->lastName();
        $firstname = $this->faker->firstNameMale();
        $username = $this->slugger->slug($lastname . ' '  . $firstname)->lower();
        static $total = 0;
        $user = new User();
        $user->setUsername($username);
        $user->setLastname($lastname);
        $user->setFirstname($firstname);
        $user->setAddress($this->faker->address());
        $user->setZipcode($this->faker->postcode());
        $user->setCity($this->faker->city());
        $user->setEmail($this->faker->email());
        $user->setNumber($this->faker->phoneNumber());
        $user->setPassword($this->faker->password());
        $user->setActive(True);
        $user->setRoles(['ROLE_USER']);
        $user->setSlug($this->faker->slug());
        $user->setDescription($this->faker->text(100));
        $manager->persist($user);
        $this->setReference('user-' . $total++, $user);

        return $user;
    }
}
