<?php

namespace App\Repository;

use App\Entity\User;
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
    /**
     * VoteRepository constructor.
     * @param RegistryInterface $registry dependency injection
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Vote::class);
    }

    /**
     * @param User $user user as parameter
     */
    public function deleteVotes(User $user): void
    {
        $this->createQueryBuilder('vote')
            ->delete()
            ->where('vote.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }
}
