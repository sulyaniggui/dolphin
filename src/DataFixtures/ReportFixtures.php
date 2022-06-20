<?php

namespace App\DataFixtures;

use App\Entity\Report;
use App\Entity\Ticket;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class ReportFixtures extends Fixture implements DependentFixtureInterface
{
    private Generator $faker;
    public function __construct(){
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        $count = 6;
        for ($i = 1; $i < $count; $i++){
            $this->createReport($manager);
        }
        $manager->flush();
    }

    public function createReport(ObjectManager $manager): Report
    {
        $randomTicketId = $this->faker->numberBetween('1', '9');

        /** @var Ticket  $randomTicket */
        $randomTicket = $this->getReference('ticket-' . $randomTicketId);

        $report = new Report();
        $report->setSubject($this->faker->sentence('3'));
        $report->setTicket($randomTicket);
        $manager->persist($report);
        return $report;
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CategoryFixtures::class,
            TicketFixtures::class
        ];
    }
}
