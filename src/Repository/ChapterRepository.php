<?php

namespace App\Repository;

use App\Entity\Chapter;
use App\Entity\Story;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Chapter>
 */
class ChapterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chapter::class);
    }

    public function findByStory(Story $story, $orderBy = ['position' => 'ASC'])
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.story = :story')
            ->setParameter('story', $story)
            ->orderBy('c.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findFirstChapter(Story $story)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.story = :story')
            ->setParameter('story', $story)
            ->orderBy('c.position', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findNextChapter(Chapter $currentChapter)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.story = :story')
            ->andWhere('c.position > :position')
            ->setParameter('story', $currentChapter->getStory())
            ->setParameter('position', $currentChapter->getPosition())
            ->orderBy('c.position', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findPreviousChapter(Chapter $currentChapter)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.story = :story')
            ->andWhere('c.position < :position')
            ->setParameter('story', $currentChapter->getStory())
            ->setParameter('position', $currentChapter->getPosition())
            ->orderBy('c.position', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getMaxPosition(Story $story): int
    {
        $result = $this->createQueryBuilder('c')
            ->select('MAX(c.position)')
            ->andWhere('c.story = :story')
            ->setParameter('story', $story)
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? (int) $result : 0;
    }
}
