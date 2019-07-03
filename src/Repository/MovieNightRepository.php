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
            ->andWhere('m.date > :currentDate or (m.time > :currentTime and m.date = :currentDate)')
            ->setParameter('currentDate', date('Y-m-d'))
            ->setParameter('currentTime', date('H:i', time()-900))
            ->orderBy('m.date', 'ASC')
            ->addOrderBy('m.time', 'ASC')
            ->getQuery()
        ;

        return $qb->getResult();
    }

    /*
     *  - retrieve next movienight or null if none planned
     */
    /**
     * @param int $offset offset = next movienight
     *
     * @return MovieNight|null
     *
     * @throws NonUniqueResultException
     */
    public function getNextMovienight($offset = 0)  : ?MovieNight
    {
        $qb = $this->createQueryBuilder('m')
            ->andWhere('m.date = :currentDate')
            ->setParameter('currentDate', date('Y-m-d'))
            ->andWhere('m.time > :currentTime')
            ->setParameter('currentTime', date('H:i', time() - 900))
            ->orWhere('m.date > :currentDate')
            ->setParameter('currentDate', date('Y-m-d'))
            ->orderBy('m.date', 'ASC')
            ->addOrderBy('m.time', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults(1)
            ->getQuery()
        ;

        return $qb->getOneOrNullResult();
    }
}
