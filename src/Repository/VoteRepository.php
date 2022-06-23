<?php

namespace App\Repository;

use App\Entity\Vote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Vote>
 *
 * @method Vote|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vote|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vote[]    findAll()
 * @method Vote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vote::class);
    }

    public function add(Vote $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Vote $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function checkUpvoteTicketExist($ticket, $user): array
    {
        return $this->createQueryBuilder('v')
            ->where('v.type = 1')
            ->andWhere('v.author = :user')
            ->andWhere('v.ticket = :ticket')
            ->setParameter('ticket', $ticket)
            ->setParameter('user', $user)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
            ;
    }

    public function checkDownvoteTicketExist($ticket, $user): array
    {
        return $this->createQueryBuilder('v')
            ->where('v.type = 0')
            ->andWhere('v.author = :user')
            ->andWhere('v.ticket = :ticket')
            ->setParameter('ticket', $ticket)
            ->setParameter('user', $user)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
            ;
    }

    public function checkUpvoteCommentExist($comment, $user): array
    {
        return $this->createQueryBuilder('v')
            ->where('v.type = 1')
            ->andWhere('v.author = :user')
            ->andWhere('v.comment = :comment')
            ->setParameter('comment', $comment)
            ->setParameter('user', $user)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
            ;
    }

    public function checkDownvoteCommentExist($comment, $user): array
    {
        return $this->createQueryBuilder('v')
            ->where('v.type = 0')
            ->andWhere('v.author = :user')
            ->andWhere('v.comment = :comment')
            ->setParameter('comment', $comment)
            ->setParameter('user', $user)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByUpvoteTicket($value): array
{
    return $this->createQueryBuilder('v')
        ->where('v.type = 1')
        ->andWhere('v.active = true')
        ->andWhere('v.ticket = :val')
        ->setParameter('val', $value)
        ->orderBy('v.id', 'ASC')
        ->setMaxResults(10)
        ->getQuery()
        ->getResult()
    ;
}

    public function findByDownvoteTicket($value): array
    {
        return $this->createQueryBuilder('v')
            ->where('v.type = 0')
            ->andWhere('v.ticket = :val')
            ->andWhere('v.active = true')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByUpvoteComment($value): array
    {
        return $this->createQueryBuilder('v')
            ->where('v.type = 1')
            ->andWhere('v.active = true')
            ->andWhere('v.comment = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByDownvoteComment($value): array
    {
        return $this->createQueryBuilder('v')
            ->where('v.type = 0')
            ->andWhere('v.comment = :val')
            ->andWhere('v.active = true')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }

//    /**
//     * @return Vote[] Returns an array of Vote objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Vote
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
