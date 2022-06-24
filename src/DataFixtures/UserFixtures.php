<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use PhpParser\Node\Expr\Array_;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserFixtures extends Fixture
{
    private Generator $faker;

    public function __construct(private SluggerInterface $slugger, private UserPasswordHasherInterface $passwordHasher){
        $faker = Factory::create('fr_FR');
        $this->faker = $faker;
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $count = $this->faker->numberBetween(10, 20);
        for($i = 1; $i <= $count; $i++){
            $this->createUser($manager);
        }

        $preSetUser = new User();
        $preSetUser->setUsername('david');
        $preSetUser->setLastname('David');
        $preSetUser->setFirstname('Pathiashvili');
        $preSetUser->setAddress($this->faker->address());
        $preSetUser->setZipcode($this->faker->postcode());
        $preSetUser->setCity($this->faker->city());
        $preSetUser->setEmail($this->faker->email());
        $preSetUser->setNumber($this->faker->phoneNumber());
        $passwordPreSetUser = '1234';
        $hashedPasswordPreSetUser = $this->passwordHasher->hashPassword(
            $preSetUser,
            $passwordPreSetUser
        );
        $preSetUser->setPassword($hashedPasswordPreSetUser);
        $preSetUser->setActive(True);
        $preSetUser->setRoles(['ROLE_USER']);
        $preSetUser->setSlug($this->faker->slug());
        $preSetUser->setDescription($this->faker->text(100));
        $manager->persist($preSetUser);

        $manager->flush();
    }

    public function createUser(ObjectManager $manager): User
    {
        $lastname = $this->faker->lastName();
        $firstname = $this->faker->firstNameMale();
        $username = $this->slugger->slug($lastname . ' '  . $firstname)->lower();
        static $total = 0;
        $user = new User();
        $password = $this->faker->password();
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $password
        );
        $user->setUsername($username);
        $user->setLastname($lastname);
        $user->setFirstname($firstname);
        $user->setAddress($this->faker->address());
        $user->setZipcode($this->faker->postcode());
        $user->setCity($this->faker->city());
        $user->setEmail($this->faker->email());
        $user->setNumber($this->faker->phoneNumber());
        $user->setPassword($hashedPassword);
        $user->setActive(True);
        $user->setRoles(['ROLE_USER']);
        $user->setSlug($this->faker->slug());
        $user->setDescription($this->faker->text(100));
        $manager->persist($user);
        $this->setReference('user-' . $total++, $user);

        return $user;
    }
}
