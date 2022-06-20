<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\String\Slugger\SluggerInterface;

class TicketFixtures extends Fixture implements DependentFixtureInterface
{
    private Generator $faker;

    public function __construct(private SluggerInterface $slugger)
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        $count = 11;
        for ($i = 1; $i < $count; $i++){
            $this->createTicket($manager);
        }
        $manager->flush();
    }

    public function createTicket(ObjectManager $manager): Ticket
    {

        static $total = 0;
        $randomCategoryId = $this->faker->numberBetween('1', '9');
        /** @var Category $randomCategory */
        $randomCategory = $this->getReference('category-' . $randomCategoryId);

        $randomUserId = $this->faker->numberBetween('1', '9');
        /** @var User $randomAuthor */
        $randomAuthor = $this->getReference('user-' . $randomUserId);

        $title = $this->faker->sentence(3);

        $ticket = new Ticket();
        $ticket->setTitle($title);
        $ticket->setDescription($this->faker->text(100));
        $ticket->setSlug($this->slugger->slug($title)->lower());
        $ticket->setUpdatedAt(new \DateTime());
        $ticket->setAuthor($randomAuthor);
        $ticket->setCategory($randomCategory);
        $manager->persist($ticket);

        $this->setReference('ticket-' . $total++, $ticket);
        return $ticket;
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CategoryFixtures::class
        ];
    }
}
