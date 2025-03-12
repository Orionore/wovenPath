<?php

namespace App\Repository;

use App\Entity\Story;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

class StoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Story::class);
    }

    /**
     * Trouve toutes les histoires publiées
     */
    public function findPublished()
    {
        return $this->createQueryBuilder('s')
            ->where('s.status = :status')
            ->andWhere('s.deletedAt IS NULL')
            ->setParameter('status', true)
            ->orderBy('s.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les histoires publiées par un utilisateur spécifique
     */
    public function findPublishedByUser(int $userId)
    {
        return $this->createQueryBuilder('s')
            ->where('s.user_id = :userId')
            ->andWhere('s.status = :status')
            ->andWhere('s.deletedAt IS NULL')
            ->setParameter('userId', $userId)
            ->setParameter('status', true)
            ->orderBy('s.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve toutes les histoires (publiées et brouillons) d'un utilisateur
     */
    public function findAllByUser(int $userId)
    {
        return $this->createQueryBuilder('s')
            ->where('s.user_id = :userId')
            ->andWhere('s.deletedAt IS NULL')
            ->setParameter('userId', $userId)
            ->orderBy('s.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findLatestPublished(int $page = 1, int $limit = 10): Paginator
    {
        $query = $this->createQueryBuilder('s')
            ->where('s.status = :status')
            ->andWhere('s.deletedAt IS NULL')
            ->setParameter('status', true)
            ->orderBy('s.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery();

        return new Paginator($query);
    }

    public function searchStories(?string $query = null, ?string $genre = null, int $page = 1, int $limit = 10): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->where('s.status = :status')
            ->andWhere('s.deletedAt IS NULL')
            ->setParameter('status', true);

        if ($query) {
            $queryBuilder
                ->andWhere('s.title LIKE :query OR s.description LIKE :query')
                ->setParameter('query', '%' . $query . '%');
        }

        if ($genre && $genre !== '') {
            $queryBuilder
                ->andWhere('s.genre LIKE :genre OR s.genre LIKE :genreStart OR s.genre LIKE :genreMiddle OR s.genre LIKE :genreEnd')
                ->setParameter('genre', $genre)
                ->setParameter('genreStart', $genre.',%')
                ->setParameter('genreMiddle', '%,'.$genre.',%')
                ->setParameter('genreEnd', '%,'.$genre);
        }

        $queryBuilder
            ->orderBy('s.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return new Paginator($queryBuilder->getQuery());
    }
}
