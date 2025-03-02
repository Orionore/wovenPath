<?php

namespace App\Repository;

use App\Entity\Story;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Story>
 */
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
}
