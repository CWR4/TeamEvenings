<?php

namespace App\Repository;

use App\Entity\MovieNight;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
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

    // /**
    //  * @return MovieNight[] Returns an array of MovieNight objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MovieNight
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findAllByDateAsc()  : array
    {
        $qb = $this->createQueryBuilder('m')
            ->andWhere('m.date > :currentdate or (m.time > :currenttime and m.date = :currentdate)')
            ->setParameter('currentdate', date('Y-m-d'))
            ->setParameter('currenttime', date('H:i'))
            ->orderBy('m.date', 'ASC')
            ->addOrderBy('m.time', 'ASC')
            ->getQuery()
        ;

        return $qb->getResult();
    }

    public function getNextMovienight()  : ?MovieNight
    {
        $qb = $this->createQueryBuilder('m')
            ->andWhere('m.date >= :currentdate')
            ->setParameter('currentdate', date('Y-m-d'))
            ->andWhere('m.time > :currenttime')
            ->setParameter('currenttime', date('H:i', time() - 1800))
            ->orderBy('m.date', 'ASC')
            ->addOrderBy('m.time', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
        ;

        return $qb->getOneOrNullResult();
    }
}
