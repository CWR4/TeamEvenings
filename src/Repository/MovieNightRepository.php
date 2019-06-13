<?php

namespace App\Repository;

use App\Entity\MovieNight;
use App\Entity\Voting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MovieNight|null find($id, $lockMode = null, $lockVersion = null)
 * @method MovieNight|null findOneBy(array $criteria, array $orderBy = null)
 * @method MovieNight[]    findAll()
 * @method MovieNight[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovieNightRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MovieNight::class);
    }

    /*
     *  - retrieve all future movienights
     *  - ordered ascending by date
     */
    public function findAllByDateAsc()  : array
    {
        $qb = $this->createQueryBuilder('m')
            ->andWhere('m.date > :currentdate or (m.time > :currenttime and m.date = :currentdate)')
            ->setParameter('currentdate', date('Y-m-d'))
            ->setParameter('currenttime', date('H:i', time()-900))
            ->orderBy('m.date', 'ASC')
            ->addOrderBy('m.time', 'ASC')
            ->getQuery()
        ;

        return $qb->getResult();
    }

    /*
     *  - retrieve next movienight or null if none planned
     */
    public function getNextMovienight()  : ?MovieNight
    {
        $qb = $this->createQueryBuilder('m')
            ->andWhere('m.date >= :currentdate')
            ->setParameter('currentdate', date('Y-m-d'))
            ->andWhere('m.time > :currenttime')
            ->setParameter('currenttime', date('H:i', time() - 900))
            ->orderBy('m.date', 'ASC')
            ->addOrderBy('m.time', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
        ;

        return $qb->getOneOrNullResult();
    }
}
