<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure;

use App\Entity\BlogArticleEntity;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineBlogArticleRepository
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /** @return list<BlogArticleEntity> */
    public function findPublished(): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('article')
            ->from(BlogArticleEntity::class, 'article')
            ->andWhere('article.publishedAt <= :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('article.publishedAt', 'DESC')
            ->addOrderBy('article.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findPublishedBySlug(string $slug): ?BlogArticleEntity
    {
        return $this->entityManager->createQueryBuilder()
            ->select('article')
            ->from(BlogArticleEntity::class, 'article')
            ->andWhere('article.slug = :slug')
            ->andWhere('article.publishedAt <= :now')
            ->setParameter('slug', $slug)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getOneOrNullResult();
    }
}
