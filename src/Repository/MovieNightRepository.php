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
            ->andWhere('m.date = :currentdate')
            ->setParameter('currentdate', date('Y-m-d'))
            ->andWhere('m.time > :currenttime')
            ->setParameter('currenttime', date('H:i', time() - 900))
            ->orWhere('m.date > :currentdate')
            ->setParameter('currentdate', date('Y-m-d'))
            ->orderBy('m.date', 'ASC')
            ->addOrderBy('m.time', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults(1)
            ->getQuery()
        ;

        return $qb->getOneOrNullResult();
    }
}
