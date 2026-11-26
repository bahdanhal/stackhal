<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Blog\Application\BlogArticleRepository;
use App\Blog\Domain\BlogArticle;
use App\Shared\Presentation\Http\PolishLegacyRedirectController;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class PolishLegacyRedirectControllerTest extends TestCase
{
    #[DataProvider('staticRedirectProvider')]
    public function testStaticPolishPathsRedirectPermanentlyToEnglish(string $polishPath, string $expectedTarget): void
    {
        $controller = new PolishLegacyRedirectController();
        $response = $controller->redirectStatic(Request::create('https://stackhal.com' . $polishPath));

        self::assertSame(Response::HTTP_MOVED_PERMANENTLY, $response->getStatusCode());
        self::assertSame($expectedTarget, $response->getTargetUrl());
    }

    /** @return iterable<string, array{string, string}> */
    public static function staticRedirectProvider(): iterable
    {
        yield 'root without slash' => ['/pl', '/'];
        yield 'root with slash' => ['/pl/', '/'];
        yield 'tools' => ['/pl/narzedzia', '/'];
        yield 'geo audit' => ['/pl/audyt-geo', '/geo-audit'];
        yield 'seo audit' => ['/pl/audyt-seo', '/seo-audit'];
        yield 'pkpass inspector' => ['/pl/inspektor-pkpass', '/apple-pkpass-inspector'];
        yield 'ai studio sync' => ['/pl/synchronizacja-plikow-ai-studio', '/ai-studio-local-file-sync'];
        yield 'blog index' => ['/pl/blog', '/blog'];
        yield 'agent conversations' => ['/pl/rozmowy-agentow', '/agent-conversations'];
        yield 'agent conversations feed' => ['/pl/rozmowy-agentow/feed.xml', '/agent-conversations/feed.xml'];
    }

    public function testPolishBlogArticleRedirectsMappedSlugToEnglish(): void
    {
        $englishArticle = $this->article('what-agents-are-talking-about-2026-10-02', 'en');
        $repo = $this->createStub(BlogArticleRepository::class);
        $repo->method('findPublishedBySlug')->willReturnCallback(
            static fn (string $slug, string $locale): ?BlogArticle =>
                $slug === 'what-agents-are-talking-about-2026-10-02' && $locale === 'en' ? $englishArticle : null
        );

        $controller = new PolishLegacyRedirectController();
        $response = $controller->redirectBlogArticle('o-czym-rozmawiaja-agenci-2026-10-02', $repo);

        self::assertSame(Response::HTTP_MOVED_PERMANENTLY, $response->getStatusCode());
        self::assertSame('/blog/what-agents-are-talking-about-2026-10-02', $response->getTargetUrl());
    }

    public function testPolishBlogArticleRedirectsMatchingEnglishSlug(): void
    {
        $englishArticle = $this->article('bmad-vs-gsd-ai-agent-frameworks-benchmark', 'en');
        $repo = $this->createStub(BlogArticleRepository::class);
        $repo->method('findPublishedBySlug')->willReturnCallback(
            static fn (string $slug, string $locale): ?BlogArticle =>
                $slug === 'bmad-vs-gsd-ai-agent-frameworks-benchmark' && $locale === 'en' ? $englishArticle : null
        );

        $controller = new PolishLegacyRedirectController();
        $response = $controller->redirectBlogArticle('bmad-vs-gsd-ai-agent-frameworks-benchmark', $repo);

        self::assertSame(Response::HTTP_MOVED_PERMANENTLY, $response->getStatusCode());
        self::assertSame('/blog/bmad-vs-gsd-ai-agent-frameworks-benchmark', $response->getTargetUrl());
    }

    public function testUnknownPolishBlogArticleThrows404(): void
    {
        $repo = $this->createStub(BlogArticleRepository::class);
        $repo->method('findPublishedBySlug')->willReturn(null);

        $controller = new PolishLegacyRedirectController();

        $this->expectException(NotFoundHttpException::class);
        $controller->redirectBlogArticle('does-not-exist', $repo);
    }

    private function article(string $slug, string $locale): BlogArticle
    {
        $now = new \DateTimeImmutable();

        return new BlogArticle(
            $slug,
            'Title',
            'Description',
            'Category',
            5,
            $now,
            $now,
            '<p>Content</p>',
            '',
            '',
            '',
            [],
            [],
            $locale,
            ''
        );
    }
}
