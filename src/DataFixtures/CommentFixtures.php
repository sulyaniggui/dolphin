<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    private Generator $faker;

    public function __construct(){
        $this->faker = Factory::create('fr_FR');
    }
    public function load(ObjectManager $manager): void
    {
        $count = 11;

        for($i= 1; $i<$count; $i++){
            $this->createComment($manager);
        }
        $manager->flush();
    }

    public function createComment(ObjectManager $manager): Comment
    {

        static $total = 0;
        $randomTicketId = $this->faker->numberBetween('1', '9');
        /** @var Ticket $randomTicket */
        $randomTicket = $this->getReference('ticket-' . $randomTicketId);

        $randomUserId = $this->faker->numberBetween('1', '9');
        /** @var User $randomAuthor */
        $randomAuthor = $this->getReference('user-' . $randomUserId);

        $comment = new Comment();
        $comment->setContent($this->faker->text('100'));
        $comment->setTicket($randomTicket);
        $comment->setAuthor($randomAuthor);
        $comment->setUpdatedAt(new \DateTime('NOW'));
        $manager->persist($comment);
        $this->setReference('comment-' . $total++ , $comment);


        return $comment;
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CategoryFixtures::class,
            TicketFixtures::class,
        ];
    }
}
