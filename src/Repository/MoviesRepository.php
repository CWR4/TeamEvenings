<?php

namespace App\Repository;

use App\Entity\Movie;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Movie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Movie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Movie[]    findAll()
 * @method Movie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MoviesRepository extends ServiceEntityRepository
{
    /**
     * MoviesRepository constructor.
     * @param RegistryInterface $registry dependency injection
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Movie::class);
    }

    /**
     *  - retrieve movie by imdb ID or null if not found
     *
     * @param int $id online movie database id of movie
     *
     * @throws NonUniqueResultException
     *
     * @return Movie|null
     */
    public function findByImdbId($id) : ?Movie
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.imdbID = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
