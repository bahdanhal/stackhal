<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Shared\Presentation\Http\SitemapController;
use PHPUnit\Framework\TestCase;

final class SitemapControllerTest extends TestCase
{
    public function testGeneratesValidEnglishOnlyXmlSitemapWithHeaders(): void
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
        self::assertStringContainsString('https://stackhal.com/apple-pkpass-inspector', $content);
        self::assertStringContainsString('https://stackhal.com/ai-studio-local-file-sync', $content);
        self::assertStringContainsString('https://stackhal.com/agent-conversations', $content);
        self::assertStringContainsString('https://stackhal.com/blog', $content);
        self::assertStringNotContainsString('/pl/', $content);
        self::assertStringNotContainsString('hreflang="pl"', $content);
        self::assertStringNotContainsString('/caddy-transpiler', $content);
        self::assertStringNotContainsString('/regex-transpiler', $content);
        self::assertStringNotContainsString('/favicon-suite', $content);
        self::assertStringNotContainsString('/cors-sandbox', $content);
        self::assertStringNotContainsString('/bimi-studio', $content);
        self::assertStringNotContainsString('/domain-inspector', $content);
        self::assertStringNotContainsString('/cidr-subnet-matrix', $content);
        self::assertStringNotContainsString('/dns-dag-tracer', $content);
        self::assertStringNotContainsString('/app-links-validator', $content);
        self::assertStringNotContainsString('/composer-license-checker', $content);
        self::assertStringNotContainsString('/composer-license-metadata-dependency-audit', $content);
        self::assertStringNotContainsString('/domain-security', $content);
    }

    public function testSitemapIncludesOnlyEnglishBlogArticlesWithLastmod(): void
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
            ->willReturnCallback(static fn (?string $locale): array => $locale === 'pl' ? [$plArticle] : [$enArticle]);

        $controller = new SitemapController();
        $content = (string) $controller($repo)->getContent();

        self::assertSame(1, substr_count($content, '<loc>https://stackhal.com/blog/en-slug</loc>'));
        self::assertSame(1, substr_count($content, '<loc>https://stackhal.com/blog</loc>'));
        self::assertStringNotContainsString('/pl/', $content);
        self::assertStringContainsString(
            '<loc>https://stackhal.com/blog/en-slug</loc><lastmod>2026-09-17</lastmod>',
            $content
        );
        self::assertStringContainsString(
            '<loc>https://stackhal.com/blog</loc><lastmod>2026-09-17</lastmod>',
            $content
        );
    }
}
