<?php

declare(strict_types=1);

namespace App\Tests\Blog;

use App\Blog\Infrastructure\DoctrineBlogArticleRepository;
use App\Blog\Domain\BlogArticle;
use App\Entity\BlogArticleEntity;
use App\Tests\DoctrineTestCase;

final class DoctrineBlogArticleRepositoryTest extends DoctrineTestCase
{
    public function testFindPublishedOrdersArticlesAndExcludesFutureEntries(): void
    {
        $now = new \DateTimeImmutable();
        $this->entityManager->persist($this->article('newer', $now->modify('-1 day')));
        $this->entityManager->persist($this->article('older', $now->modify('-2 days')));
        $this->entityManager->persist($this->article('future', $now->modify('+1 day')));
        $this->entityManager->flush();

        $articles = $this->repository()->findPublished();

        self::assertSame(['newer', 'older'], array_map(
            static fn (BlogArticle $article): string => $article->getSlug(),
            $articles
        ));
    }

    public function testFindPublishedBySlugReturnsOnlyPublishedArticle(): void
    {
        $this->entityManager->persist($this->article('available', new \DateTimeImmutable('-1 hour')));
        $this->entityManager->persist($this->article('scheduled', new \DateTimeImmutable('+1 hour')));
        $this->entityManager->flush();

        self::assertSame('available', $this->repository()->findPublishedBySlug('available')?->getSlug());
        self::assertNull($this->repository()->findPublishedBySlug('scheduled'));
        self::assertNull($this->repository()->findPublishedBySlug('missing'));
    }

    public function testFindPublishedBySlugStrictlyMatchesLocaleWithoutEnglishFallback(): void
    {
        $this->entityManager->persist($this->article('english-only', new \DateTimeImmutable('-1 hour'), 'en'));
        $this->entityManager->flush();

        self::assertSame('english-only', $this->repository()->findPublishedBySlug('english-only', 'en')?->getSlug());
        self::assertNull($this->repository()->findPublishedBySlug('english-only', 'pl'));
    }

    public function testDigestArchiveFiltersLocaleCategoryAndScheduledEditions(): void
    {
        $english = $this->article('digest-en', new \DateTimeImmutable('-1 hour'), 'en');
        $polish = $this->article('digest-pl', new \DateTimeImmutable('-1 hour'), 'pl');
        $future = $this->article('future-digest', new \DateTimeImmutable('+1 day'), 'en');
        foreach ([$english, $polish, $future] as $digest) {
            $digest->setCategory(BlogArticle::AGENT_CONVERSATIONS_CATEGORY);
            $this->entityManager->persist($digest);
        }
        $this->entityManager->persist($this->article('ordinary-guide', new \DateTimeImmutable('-1 hour')));
        $this->entityManager->flush();

        $repository = $this->repository();
        $editions = $repository->findPublished('en', BlogArticle::AGENT_CONVERSATIONS_CATEGORY);
        self::assertSame(['digest-en'], array_map(static fn (BlogArticle $article): string => $article->getSlug(), $editions));
        self::assertTrue($editions[0]->isAgentConversationDigest());
        self::assertSame('digest-pl', $repository->findPublished('pl', BlogArticle::AGENT_CONVERSATIONS_CATEGORY)[0]->getSlug());
        self::assertCount(3, $repository->findPublished());
    }

    private function repository(): DoctrineBlogArticleRepository
    {
        return new DoctrineBlogArticleRepository($this->entityManager);
    }

    private function article(
        string $slug,
        \DateTimeImmutable $publishedAt,
        string $locale = 'en',
        string $alternateSlug = ''
    ): BlogArticleEntity {
        return new BlogArticleEntity(
            $slug,
            $slug,
            'Description',
            'DNS',
            5,
            $publishedAt,
            $publishedAt,
            '<p>Content</p>',
            'Open tool',
            '/tool',
            'dns',
            ['one', 'two'],
            [['name' => 'Step', 'text' => 'Do the thing']],
            $locale,
            $alternateSlug
        );
    }
}
