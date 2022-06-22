<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminFixtures extends Fixture implements DependentFixtureInterface
{
    private Generator $faker;

    public function __construct(private SluggerInterface $slugger, private UserPasswordHasherInterface $passwordHasher){
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        $this->createAdmin($manager);
        $manager->flush();
    }

    public function createAdmin(ObjectManager $manager): User
    {
        $admin = new User();

        $username = 'sulyan';
        $firstname = "Sulyan";
        $lastname = 'IGGUI';
        $password = '1234';
        $hashedPassword = $this->passwordHasher->hashPassword(
            $admin,
            $password
        );
        $admin->setUsername($username);
        $admin->setLastname($lastname);
        $admin->setFirstname($firstname);
        $admin->setAddress($this->faker->address());
        $admin->setZipcode($this->faker->postcode());
        $admin->setCity($this->faker->city());
        $admin->setEmail($this->faker->email());
        $admin->setNumber($this->faker->phoneNumber());
        $admin->setPassword($hashedPassword);
        $admin->setActive(True);
        $admin->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $admin->setSlug($this->faker->slug());
        $admin->setDescription($this->faker->text(100));

        $manager->persist($admin);
        return $admin;
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CategoryFixtures::class,
            TicketFixtures::class,
            ReportFixtures::class,
            CommentFixtures::class,
            VoteFixtures::class
        ];
    }
}
