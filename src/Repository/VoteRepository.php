<?php

namespace App\Repository;

use App\Entity\Vote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Vote|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vote|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vote[]    findAll()
 * @method Vote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VoteRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Vote::class);
    }

    // Returns number of votes in voting for one movie
    public function numVotes($votingId, $movieId) : int
    {
        return $this->createQueryBuilder('vote')
            ->select('count(vote.id)')
            ->andWhere('vote.Voting = :vid')
            ->setParameter('vid', $votingId)
            ->andWhere('vote.Movie = :mid')
            ->setParameter('mid', $movieId)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    // Find out if logged in User already voted
    public function hasVoted($votingId, $user) : ?Vote
    {
        return $this->createQueryBuilder('vote')
            ->andWhere('vote.Voting = :vid')
            ->setParameter('vid', $votingId)
            ->andWhere('vote.User = :user')
            ->setParameter('user', $user)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
