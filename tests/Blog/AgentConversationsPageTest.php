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
    public function testLocalizedArchiveEditionAndFeedRenderThroughRealRoutes(): void
    {
        $kernel = self::bootKernel();
        self::getContainer()->set('logger', new NullLogger());
        $articles = ['en' => $this->edition('en'), 'pl' => $this->edition('pl')];
        $repository = $this->createMock(BlogArticleRepository::class);
        $repository->expects(self::exactly(4))->method('findPublished')->willReturnCallback(
            static function (?string $locale, ?string $category) use ($articles): array {
                self::assertSame(BlogArticle::AGENT_CONVERSATIONS_CATEGORY, $category);
                self::assertNotNull($locale);
                self::assertArrayHasKey($locale, $articles);
                return [$articles[$locale]];
            }
        );
        $repository->expects(self::exactly(4))->method('findPublishedBySlug')->willReturnCallback(
            static fn (string $slug, string $locale): ?BlogArticle =>
                $articles[$locale]->getSlug() === $slug ? $articles[$locale] : null
        );
        self::getContainer()->set(BlogArticleRepository::class, $repository);

        foreach (['en' => '/agent-conversations', 'pl' => '/pl/rozmowy-agentow'] as $locale => $archive) {
            $response = $kernel->handle(Request::create('https://stackhal.com' . $archive));
            self::assertSame(200, $response->getStatusCode());
            $html = (string) $response->getContent();
            self::assertStringContainsString('href="https://stackhal.com' . $archive . '"', $html);
            self::assertStringContainsString('hreflang="en"', $html);
            self::assertStringContainsString('hreflang="pl"', $html);
            self::assertStringNotContainsString('agent_digest.', $html);
            self::assertStringContainsString($locale === 'pl' ? 'O czym rozmawiają agenci' : 'What agents are talking about', $html);

            $feed = $kernel->handle(Request::create('https://stackhal.com' . $archive . '/feed.xml'));
            self::assertSame(200, $feed->getStatusCode());
            self::assertSame('application/rss+xml; charset=UTF-8', $feed->headers->get('Content-Type'));
            $document = new \DOMDocument();
            self::assertTrue($document->loadXML((string) $feed->getContent()));
            $xpath = new \DOMXPath($document);
            self::assertSame($locale, $xpath->evaluate('string(/rss/channel/language)'));
            self::assertSame('Pigeons & paintings <today>', $xpath->evaluate('string(/rss/channel/item/title)'));
            self::assertSame(1.0, $xpath->evaluate('count(/rss/channel/item)'));

            $editionPath = ($locale === 'pl' ? '/pl/blog/' : '/blog/') . $articles[$locale]->getSlug();
            $edition = $kernel->handle(Request::create('https://stackhal.com' . $editionPath));
            self::assertSame(200, $edition->getStatusCode());
            $editionHtml = (string) $edition->getContent();
            self::assertStringContainsString('digest-edition', $editionHtml);
            self::assertStringContainsString('href="https://stackhal.com' . $editionPath . '"', $editionHtml);
            self::assertStringContainsString('hreflang="' . ($locale === 'pl' ? 'en' : 'pl') . '"', $editionHtml);
            self::assertStringNotContainsString('Related tool', $editionHtml);
            self::assertStringNotContainsString('agent_digest.', $editionHtml);
        }
    }

    private function edition(string $locale): BlogArticle
    {
        $date = new \DateTimeImmutable('2026-10-02T16:00:00Z');
        return new BlogArticle(
            'edition-' . $locale,
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
            $locale,
            'edition-' . ($locale === 'pl' ? 'en' : 'pl')
        );
    }
}
