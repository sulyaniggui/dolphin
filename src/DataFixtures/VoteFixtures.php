<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Ticket;
use App\Entity\User;
use App\Entity\Vote;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class VoteFixtures extends Fixture implements DependentFixtureInterface
{
    private Generator $faker;
    public function __construct(){
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        $count = 11;

        for($i = 1; $i < $count; $i++){
            $randomAuthorId = $this->faker->numberBetween('1', '9');
            /** @var User $randomAuthor */
            $randomAuthor = $this->getReference('user-' . $randomAuthorId);
            if ($this->faker->boolean === true){
                $this->createVoteLinkToTicket($manager, $randomAuthor);
            } else {
                $this->createVoteLinkToComment($manager, $randomAuthor);
            }
        }
        $manager->flush();
    }

    public function createVoteLinkToComment(ObjectManager $manager, User $randomAuthor): Vote
    {
        $randomCommentId = $this->faker->numberBetween('1', '9');
        /** @var Comment $randomComment */
        $randomComment = $this->getReference('comment-' . $randomCommentId);

        $vote = new Vote();
        $vote->setType($this->faker->numberBetween('0', '1'));
        $vote->setComment($randomComment);
        $vote->setAuthor($randomAuthor);
        $manager->persist($vote);
        return $vote;
    }

    public function createVoteLinkToTicket(ObjectManager $manager, User $randomAuthor): Vote
    {

        $randomTicketId = $this->faker->numberBetween('1', '9');
        /** @var Ticket $randomTicket */
        $randomTicket = $this->getReference('ticket-' . $randomTicketId);
        $vote = new Vote();
        $vote->setType($this->faker->numberBetween('0', '1'));
        $vote->setTicket($randomTicket);
        $vote->setAuthor($randomAuthor);
        $manager->persist($vote);

        return $vote;
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CategoryFixtures::class,
            TicketFixtures::class,
            CommentFixtures::class,
            ReportFixtures::class,
        ];
    }
}
