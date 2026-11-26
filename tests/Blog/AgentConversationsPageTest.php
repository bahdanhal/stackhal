<?php

declare(strict_types=1);

namespace App\Tests\Blog;

use App\Blog\Application\BlogArticleRepository;
use App\Blog\Domain\BlogArticle;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Psr\Log\NullLogger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

final class AgentConversationsPageTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return \App\Kernel::class;
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testArchiveEditionAndFeedRenderThroughRealRoutes(): void
    {
        $kernel = self::bootKernel();
        self::getContainer()->set('logger', new NullLogger());
        $article = $this->edition();
        $repository = $this->createMock(BlogArticleRepository::class);
        $repository->expects(self::exactly(2))->method('findPublished')->willReturnCallback(
            static function (?string $locale, ?string $category) use ($article): array {
                self::assertSame(BlogArticle::AGENT_CONVERSATIONS_CATEGORY, $category);
                self::assertSame('en', $locale);
                return [$article];
            }
        );
        $repository->expects(self::once())->method('findPublishedBySlug')->willReturnCallback(
            static fn (string $slug, string $locale): ?BlogArticle =>
                $locale === 'en' && $article->getSlug() === $slug ? $article : null
        );
        self::getContainer()->set(BlogArticleRepository::class, $repository);

        $response = $kernel->handle(Request::create('https://stackhal.com/agent-conversations'));
        self::assertSame(200, $response->getStatusCode());
        $html = (string) $response->getContent();
        self::assertStringContainsString('href="https://stackhal.com/agent-conversations"', $html);
        self::assertStringNotContainsString('hreflang="pl"', $html);
        self::assertStringNotContainsString('agent_digest.', $html);
        self::assertStringContainsString('What agents are talking about', $html);

        $feed = $kernel->handle(Request::create('https://stackhal.com/agent-conversations/feed.xml'));
        self::assertSame(200, $feed->getStatusCode());
        self::assertSame('application/rss+xml; charset=UTF-8', $feed->headers->get('Content-Type'));
        $document = new \DOMDocument();
        self::assertTrue($document->loadXML((string) $feed->getContent()));
        $xpath = new \DOMXPath($document);
        self::assertSame('en', $xpath->evaluate('string(/rss/channel/language)'));
        self::assertSame('Pigeons & paintings <today>', $xpath->evaluate('string(/rss/channel/item/title)'));
        self::assertSame(1.0, $xpath->evaluate('count(/rss/channel/item)'));

        $editionPath = '/blog/' . $article->getSlug();
        $edition = $kernel->handle(Request::create('https://stackhal.com' . $editionPath));
        self::assertSame(200, $edition->getStatusCode());
        $editionHtml = (string) $edition->getContent();
        self::assertStringContainsString('digest-edition', $editionHtml);
        self::assertStringContainsString('href="https://stackhal.com' . $editionPath . '"', $editionHtml);
        self::assertStringNotContainsString('hreflang="pl"', $editionHtml);
        self::assertStringNotContainsString('Related tool', $editionHtml);
        self::assertStringNotContainsString('agent_digest.', $editionHtml);
    }

    private function edition(): BlogArticle
    {
        $date = new \DateTimeImmutable('2026-10-02T16:00:00Z');
        return new BlogArticle(
            'edition-en',
            'Pigeons & paintings <today>',
            'Art & identity',
            BlogArticle::AGENT_CONVERSATIONS_CATEGORY,
            3,
            $date,
            $date,
            '<h2>A pigeon</h2><p>A source and editorial context.</p>',
            '',
            '',
            '',
            [],
            [],
            'en',
            ''
        );
    }
}
