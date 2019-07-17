<?php

namespace App\Repository;

use App\Entity\MovieNight;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;

use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MovieNight|null find($id, $lockMode = null, $lockVersion = null)
 * @method MovieNight|null findOneBy(array $criteria, array $orderBy = null)
 * @method MovieNight[]    findAll()
 * @method MovieNight[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovieNightRepository extends ServiceEntityRepository
{
    /**
     * MovieNightRepository constructor.
     * @param RegistryInterface $registry dependency injection
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MovieNight::class);
    }

    /*
     *  - retrieve all future movienights
     *  - ordered ascending by date
     */
    /**
     * @return array
     */
    public function findAllByDateAsc()  : array
    {
        $qb = $this->createQueryBuilder('m')
            ->andWhere('m.dateAndTime >= :currentDateAndTime')
            ->setParameter('currentDateAndTime', date('Y-m-d H:i'))
            ->orderBy('m.dateAndTime', 'ASC')
            ->getQuery()
        ;

        return $qb->getResult();
    }

    /*
     *  - retrieve next movienight or null if none planned
     */
    /**
     * @return MovieNight|null
     *
     * @throws NonUniqueResultException
     */
    public function getNextMovienight()  : ?MovieNight
    {
        $qb = $this->createQueryBuilder('m')
            ->andWhere('m.dateAndTime >= :currentDateAndTime')
            ->setParameter('currentDateAndTime', date('Y-m-d H:i'))
            ->andWhere('m.movies is not empty')
            ->orderBy('m.dateAndTime', 'ASC')
            ->getQuery()
        ;

        return $qb->getOneOrNullResult();
    }
}
