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
    public function findPublished(?string $locale = null): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('article')
            ->from(BlogArticleEntity::class, 'article')
            ->andWhere('article.publishedAt <= :now')
            ->setParameter('now', new \DateTimeImmutable());

        if ($locale !== null && $locale !== '') {
            $qb->andWhere('article.locale = :locale')
                ->setParameter('locale', $locale);
        }

        /** @var list<BlogArticleEntity> $articles */
        $articles = $qb->orderBy('article.publishedAt', 'DESC')
            ->addOrderBy('article.id', 'DESC')
            ->getQuery()
            ->getResult();

        return array_map($this->map(...), $articles);
    }

    public function findPublishedBySlug(string $slug, string $locale = 'en'): ?BlogArticle
    {
        /** @var BlogArticleEntity|null $article */
        $article = $this->entityManager->createQueryBuilder()
            ->select('article')
            ->from(BlogArticleEntity::class, 'article')
            ->andWhere('article.slug = :slug')
            ->andWhere('article.locale = :locale')
            ->andWhere('article.publishedAt <= :now')
            ->setParameter('slug', $slug)
            ->setParameter('locale', $locale)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getOneOrNullResult();

        if ($article === null && $locale !== 'en') {
            // Fallback to English version if available
            /** @var BlogArticleEntity|null $article */
            $article = $this->entityManager->createQueryBuilder()
                ->select('article')
                ->from(BlogArticleEntity::class, 'article')
                ->andWhere('article.slug = :slug')
                ->andWhere('article.locale = :locale')
                ->andWhere('article.publishedAt <= :now')
                ->setParameter('slug', $slug)
                ->setParameter('locale', 'en')
                ->setParameter('now', new \DateTimeImmutable())
                ->getQuery()
                ->getOneOrNullResult();
        }

        return $article === null ? null : $this->map($article);
    }

    /** @return list<BlogArticle> */
    public function findAllForAdmin(?string $locale = null): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('article')
            ->from(BlogArticleEntity::class, 'article');

        if ($locale !== null && $locale !== '') {
            $qb->andWhere('article.locale = :locale')
                ->setParameter('locale', $locale);
        }

        /** @var list<BlogArticleEntity> $articles */
        $articles = $qb->orderBy('article.publishedAt', 'DESC')
            ->addOrderBy('article.id', 'DESC')
            ->getQuery()
            ->getResult();

        return array_map($this->map(...), $articles);
    }

    public function findEntity(int $id): ?BlogArticleEntity
    {
        return $this->entityManager->find(BlogArticleEntity::class, $id);
    }

    public function findEntityBySlugAndLocale(string $slug, string $locale): ?BlogArticleEntity
    {
        /** @var BlogArticleEntity|null $article */
        $article = $this->entityManager->createQueryBuilder()
            ->select('article')
            ->from(BlogArticleEntity::class, 'article')
            ->andWhere('article.slug = :slug')
            ->andWhere('article.locale = :locale')
            ->setParameter('slug', $slug)
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getOneOrNullResult();

        return $article;
    }

    public function save(BlogArticleEntity $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function delete(BlogArticleEntity $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
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
            $article->getLocale(),
            $article->getAlternateSlug(),
            $article->getId(),
        );
    }
}
