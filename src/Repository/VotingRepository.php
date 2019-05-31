<?php

namespace App\Repository;

use App\Entity\Voting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Voting|null find($id, $lockMode = null, $lockVersion = null)
 * @method Voting|null findOneBy(array $criteria, array $orderBy = null)
 * @method Voting[]    findAll()
 * @method Voting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VotingRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Voting::class);
    }

    public function getNextVoting() : ?Voting
    {
        $qb = $this->createQueryBuilder('voting')
            ->andWhere('voting.id >= 1')
            ->getQuery()
            ;

        return $qb->getOneOrNullResult();
    }

    public function getVoting($votingId) : ?Voting
    {
        return $this->createQueryBuilder('voting')
            ->andWhere('voting.id = :id')
            ->setParameter('id', $votingId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
