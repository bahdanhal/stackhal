<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Shared\Presentation\Http\SitemapController;
use PHPUnit\Framework\TestCase;

final class SitemapControllerTest extends TestCase
{
    public function testGeneratesValidXmlSitemapWithHeaders(): void
    {
        $controller = new SitemapController();
        $response = $controller();

        self::assertSame(200, $response->getStatusCode());
        self::assertStringContainsString('application/xml', (string) $response->headers->get('Content-Type'));
        $content = (string) $response->getContent();
        self::assertStringContainsString('<urlset', $content);
        self::assertStringContainsString('https://stackhal.com/', $content);
        self::assertStringContainsString('https://stackhal.com/geo-audit', $content);
        self::assertStringContainsString('https://stackhal.com/seo-audit', $content);
        self::assertStringContainsString('https://stackhal.com/pl/audyt-geo', $content);
        self::assertStringContainsString('https://stackhal.com/pl/audyt-seo', $content);
        self::assertStringContainsString('https://stackhal.com/caddy-transpiler', $content);
        self::assertStringContainsString('https://stackhal.com/regex-transpiler', $content);
        self::assertStringContainsString('https://stackhal.com/favicon-suite', $content);
        self::assertStringContainsString('https://stackhal.com/cors-sandbox', $content);
        self::assertStringContainsString('https://stackhal.com/dns-dag-tracer', $content);
        self::assertStringContainsString('https://stackhal.com/app-links-validator', $content);
        self::assertStringContainsString('https://stackhal.com/ai-studio-local-file-sync', $content);
        self::assertStringContainsString('https://stackhal.com/pl/generator-favicon', $content);
        self::assertStringContainsString('https://stackhal.com/pl/piaskownica-cors', $content);
        self::assertStringContainsString('https://stackhal.com/pl/tracer-dns-dag', $content);
        self::assertStringContainsString('https://stackhal.com/pl/weryfikator-app-links', $content);
        self::assertStringContainsString('https://stackhal.com/pl/synchronizacja-plikow-ai-studio', $content);
        self::assertStringContainsString('https://stackhal.com/blog', $content);
        self::assertStringNotContainsString('/composer-license-checker', $content);
        self::assertStringNotContainsString('/composer-license-metadata-dependency-audit', $content);
        self::assertStringNotContainsString('/domain-security', $content);
    }

    public function testSitemapWithEnglishOnlyArticlesDoesNotDuplicateOrEmitFakePolishHreflang(): void
    {
        $now = new \DateTimeImmutable('2026-09-17T12:00:00+00:00');
        $article = new \App\Blog\Domain\BlogArticle(
            'single-article',
            'Title',
            'Description',
            'Category',
            5,
            $now,
            $now,
            '<p>Content</p>',
            'CTA',
            '/cta',
            'visual',
            ['line1'],
            [['name' => 'step1', 'text' => 'text1']],
            '' // No alternate slug
        );

        $repo = $this->createStub(\App\Blog\Application\BlogArticleRepository::class);
        $repo->method('findPublished')
            ->willReturnCallback(static function (?string $locale) use ($article): array {
                if ($locale === 'pl') {
                    return [];
                }
                return [$article];
            });

        $controller = new SitemapController();
        $response = $controller($repo);

        $content = (string) $response->getContent();

        // Exactly one <loc> entry for single-article
        self::assertSame(1, substr_count($content, '<loc>https://stackhal.com/blog/single-article</loc>'));

        // No Polish alternate tag referencing single-article
        self::assertStringNotContainsString('hreflang="pl" href="https://stackhal.com/blog/single-article"', $content);
        self::assertStringNotContainsString('hreflang="pl" href="https://stackhal.com/pl/blog/single-article"', $content);

        // Exactly one <loc> entry for /blog
        self::assertSame(1, substr_count($content, '<loc>https://stackhal.com/blog</loc>'));

        self::assertStringContainsString(
            '<loc>https://stackhal.com/blog/single-article</loc><lastmod>2026-09-17</lastmod>',
            $content
        );
        self::assertStringContainsString(
            '<loc>https://stackhal.com/blog</loc><lastmod>2026-09-17</lastmod>',
            $content
        );
    }

    public function testSitemapWithBilingualArticlesGeneratesPairedEntries(): void
    {
        $englishUpdatedAt = new \DateTimeImmutable('2026-09-17T12:00:00+00:00');
        $polishUpdatedAt = new \DateTimeImmutable('2026-09-18T12:00:00+00:00');
        $enArticle = new \App\Blog\Domain\BlogArticle(
            'en-slug',
            'English Title',
            'Description',
            'Category',
            5,
            $englishUpdatedAt,
            $englishUpdatedAt,
            '<p>Content</p>',
            'CTA',
            '/cta',
            'visual',
            ['line1'],
            [['name' => 'step1', 'text' => 'text1']],
            'en',
            'pl-slug'
        );
        $plArticle = new \App\Blog\Domain\BlogArticle(
            'pl-slug',
            'Polish Title',
            'Description',
            'Category',
            5,
            $polishUpdatedAt,
            $polishUpdatedAt,
            '<p>Content</p>',
            'CTA',
            '/cta',
            'visual',
            ['line1'],
            [['name' => 'step1', 'text' => 'text1']],
            'pl',
            'en-slug'
        );

        $repo = $this->createStub(\App\Blog\Application\BlogArticleRepository::class);
        $repo->method('findPublished')
            ->willReturnCallback(static function (?string $locale) use ($enArticle, $plArticle): array {
                if ($locale === 'pl') {
                    return [$plArticle];
                }
                return [$enArticle];
            });

        $controller = new SitemapController();
        $response = $controller($repo);

        $content = (string) $response->getContent();

        self::assertSame(1, substr_count($content, '<loc>https://stackhal.com/blog/en-slug</loc>'));
        self::assertSame(1, substr_count($content, '<loc>https://stackhal.com/pl/blog/pl-slug</loc>'));
        self::assertSame(1, substr_count($content, '<loc>https://stackhal.com/blog</loc>'));
        self::assertSame(1, substr_count($content, '<loc>https://stackhal.com/pl/blog</loc>'));
        self::assertStringContainsString(
            '<loc>https://stackhal.com/blog/en-slug</loc><lastmod>2026-09-17</lastmod>',
            $content
        );
        self::assertStringContainsString(
            '<loc>https://stackhal.com/pl/blog/pl-slug</loc><lastmod>2026-09-18</lastmod>',
            $content
        );
    }

    public function testUnpublishedAlternateIsNotEmittedAsAWorkingPolishUrl(): void
    {
        $now = new \DateTimeImmutable('2026-09-17T12:00:00+00:00');
        $article = new \App\Blog\Domain\BlogArticle(
            'english-article',
            'English Title',
            'Description',
            'Category',
            5,
            $now,
            $now,
            '<p>Content</p>',
            'CTA',
            '/cta',
            'visual',
            ['line1'],
            [['name' => 'step1', 'text' => 'text1']],
            'en',
            'missing-polish-article'
        );

        $repo = $this->createStub(\App\Blog\Application\BlogArticleRepository::class);
        $repo->method('findPublished')
            ->willReturnCallback(static fn (?string $locale): array => $locale === 'en' ? [$article] : []);

        $content = (string) (new SitemapController())($repo)->getContent();

        self::assertStringContainsString('https://stackhal.com/blog/english-article', $content);
        self::assertStringNotContainsString('https://stackhal.com/pl/blog/missing-polish-article', $content);
        self::assertStringNotContainsString('<loc>https://stackhal.com/pl/blog</loc>', $content);
    }
}
