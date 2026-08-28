<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure;

use App\Blog\Application\BlogArticleRepository;
use App\Blog\Domain\BlogArticle;
use App\Entity\BlogArticleEntity;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineBlogArticleRepository implements BlogArticleRepository
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /** @return list<BlogArticle> */
    public function findPublished(): array
    {
        /** @var list<BlogArticleEntity> $articles */
        $articles = $this->entityManager->createQueryBuilder()
            ->select('article')
            ->from(BlogArticleEntity::class, 'article')
            ->andWhere('article.publishedAt <= :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('article.publishedAt', 'DESC')
            ->addOrderBy('article.id', 'DESC')
            ->getQuery()
            ->getResult();

        return array_map($this->map(...), $articles);
    }

    public function findPublishedBySlug(string $slug): ?BlogArticle
    {
        /** @var BlogArticleEntity|null $article */
        $article = $this->entityManager->createQueryBuilder()
            ->select('article')
            ->from(BlogArticleEntity::class, 'article')
            ->andWhere('article.slug = :slug')
            ->andWhere('article.publishedAt <= :now')
            ->setParameter('slug', $slug)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getOneOrNullResult();

        return $article === null ? null : $this->map($article);
    }

    private function map(BlogArticleEntity $article): BlogArticle
    {
        return new BlogArticle(
            $article->getSlug(),
            $article->getTitle(),
            $article->getDescription(),
            $article->getCategory(),
            $article->getReadTimeMinutes(),
            $article->getPublishedAt(),
            $article->getUpdatedAt(),
            $article->getContentHtml(),
            $article->getCtaLabel(),
            $article->getCtaPath(),
            $article->getVisualClass(),
            $article->getVisualLines(),
            $article->getHowToSteps(),
        );
    }
}
