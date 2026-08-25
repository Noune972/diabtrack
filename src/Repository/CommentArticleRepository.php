<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\CommentArticle;
use App\Enum\CommentStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CommentArticle>
 */
class CommentArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommentArticle::class);
    }

    /**
     * Commentaires validés d'un article, du plus ancien au plus récent.
     *
     * @return CommentArticle[]
     */
    public function findValidesPourArticle(Article $article): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.article = :article')
            ->andWhere('c.status = :status')
            ->setParameter('article', $article)
            ->setParameter('status', CommentStatus::VALID)
            ->orderBy('c.date', 'ASC')
            ->addOrderBy('c.hour', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Commentaires en attente de modération, du plus récent au plus ancien.
     *
     * @return CommentArticle[]
     */
    public function findEnAttente(): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.status = :status')
            ->setParameter('status', CommentStatus::NON_VALID)
            ->orderBy('c.date', 'DESC')
            ->addOrderBy('c.hour', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
}