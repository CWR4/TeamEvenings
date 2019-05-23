<?php

namespace App\Repository;

use App\Entity\MovieNight;
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

    public function findAllByDateAsc() : array
    {
        $qb = $this->createQueryBuilder('m')
            ->andWhere('m.date > :currentdate')
            ->setParameter('currentdate', date('Y-m-d'))
            ->orderBy('m.date', 'ASC')
            ->addOrderBy('m.time', 'ASC')
            ->getQuery();

        return $qb->execute();
    }

    public function updateEntryById($id)
    {
        $qb = $this->createQueryBuilder('m')
            ->update('movie_night')
            ->where('m.id = ?id')
            ->setParameter('id', $id)
            ->getQuery();

        return $qb->execute();
    }
}
