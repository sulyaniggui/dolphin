<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Report;
use App\Entity\Ticket;
use App\Entity\User;
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
            if ($this->faker->boolean === true){
                $this->createReportLinkToTicket($manager);
            } else {
                $this->createReportLinkToComment($manager);
            }
        }
        $manager->flush();
    }






    public function createReportLinkToTicket(ObjectManager $manager): Report
    {
        $randomUserId = $this->faker->numberBetween('1', '9');

        /** @var User  $randomTicket */
        $randomUser = $this->getReference('user-' . $randomUserId);


        $randomTicketId = $this->faker->numberBetween('1', '9');

        /** @var Ticket  $randomTicket */
        $randomTicket = $this->getReference('ticket-' . $randomTicketId);

        $report = new Report();
        $report->setSubject($this->faker->sentence('3'));
        $report->setTicket($randomTicket);
        $report->setAuthor($randomUser);
        $manager->persist($report);
        return $report;
    }


    public function createReportLinkToComment(ObjectManager $manager): Report
    {
        $randomUserId = $this->faker->numberBetween('1', '9');

        /** @var User  $randomTicket */
        $randomUser = $this->getReference('user-' . $randomUserId);


        $randomCommentId = $this->faker->numberBetween('1', '9');

        /** @var Comment  $randomComment */
        $randomComment = $this->getReference('comment-' . $randomCommentId);

        $report = new Report();
        $report->setSubject($this->faker->sentence('3'));
        $report->setComment($randomComment);
        $report->setAuthor($randomUser);
        $manager->persist($report);
        return $report;
    }











    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CategoryFixtures::class,
            TicketFixtures::class,
            CommentFixtures::class
        ];
    }
}
