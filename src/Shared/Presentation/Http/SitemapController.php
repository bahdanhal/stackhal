<?php

declare(strict_types=1);

namespace App\Shared\Presentation\Http;

use App\Blog\Application\BlogArticleRepository;
use App\Blog\Domain\BlogArticle;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final readonly class SitemapController
{
    #[Route('/sitemap.xml', name: 'sitemap', methods: ['GET'])]
    public function __invoke(?BlogArticleRepository $articles = null): Response
    {
        $pairs = [
            ['/', '/pl/'],
            ['/geo-audit', '/pl/audyt-geo'],
            ['/seo-audit', '/pl/audyt-seo'],
            ['/favicon-suite', '/pl/generator-favicon'],
            ['/cors-sandbox', '/pl/piaskownica-cors'],
            ['/bimi-studio', '/pl/bimi-studio'],
            ['/domain-inspector', '/pl/inspektor-domen'],
            ['/caddy-transpiler', '/pl/konwerter-caddyfile'],
            ['/cidr-subnet-matrix', '/pl/matryca-cidr'],
            ['/regex-transpiler', '/pl/konwerter-regex'],
            ['/dns-dag-tracer', '/pl/tracer-dns-dag'],
            ['/apple-pkpass-inspector', '/pl/inspektor-pkpass'],
            ['/app-links-validator', '/pl/weryfikator-app-links'],
            ['/ai-studio-local-file-sync', '/pl/synchronizacja-plikow-ai-studio'],
        ];

        $entries = [];
        foreach ($pairs as [$en, $pl]) {
            $entries[] = $this->entry($en, $en, $pl);
            $entries[] = $this->entry($pl, $en, $pl);
        }

        $publishedEnglish = [];
        $publishedPolish = [];
        if ($articles !== null) {
            $publishedEnglish = $articles->findPublished('en');
            $publishedPolish = $articles->findPublished('pl');
        }

        if ($publishedPolish !== []) {
            $entries[] = $this->entry(
                '/blog',
                '/blog',
                '/pl/blog',
                $this->latestUpdate($publishedEnglish)
            );
            $entries[] = $this->entry(
                '/pl/blog',
                '/blog',
                '/pl/blog',
                $this->latestUpdate($publishedPolish)
            );
        } else {
            $entries[] = $this->singleEntry('/blog', 'en', $this->latestUpdate($publishedEnglish));
        }

        $polishBySlug = [];
        foreach ($publishedPolish as $article) {
            $polishBySlug[$article->getSlug()] = $article;
        }

        foreach ($publishedEnglish as $article) {
            $polishArticle = $polishBySlug[$article->getAlternateSlug()] ?? null;
            if ($polishArticle instanceof BlogArticle) {
                $entries[] = $this->entry(
                    '/blog/' . $article->getSlug(),
                    '/blog/' . $article->getSlug(),
                    '/pl/blog/' . $polishArticle->getSlug(),
                    $article->getUpdatedAt()
                );
                $entries[] = $this->entry(
                    '/pl/blog/' . $polishArticle->getSlug(),
                    '/blog/' . $article->getSlug(),
                    '/pl/blog/' . $polishArticle->getSlug(),
                    $polishArticle->getUpdatedAt()
                );
            } else {
                $entries[] = $this->singleEntry(
                    '/blog/' . $article->getSlug(),
                    'en',
                    $article->getUpdatedAt()
                );
            }
        }

        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
            . "<?xml-stylesheet type=\"text/xsl\" href=\"/sitemap.xsl\"?>\n"
            . "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:xhtml=\"http://www.w3.org/1999/xhtml\">\n"
            . implode("\n", $entries) . "\n"
            . "</urlset>\n";

        return new Response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=300, must-revalidate',
        ]);
    }

    private function singleEntry(
        string $location,
        string $locale,
        ?\DateTimeImmutable $lastModified = null
    ): string {
        $base = 'https://stackhal.com';

        $format = '  <url><loc>%s</loc>%s'
            . '<xhtml:link rel="alternate" hreflang="%s" href="%s"/>'
            . '<xhtml:link rel="alternate" hreflang="x-default" href="%s"/></url>';

        return sprintf(
            $format,
            $base . $location,
            $this->lastModified($lastModified),
            $locale,
            $base . $location,
            $base . $location
        );
    }

    private function entry(
        string $location,
        string $english,
        string $polish,
        ?\DateTimeImmutable $lastModified = null
    ): string {
        $base = 'https://stackhal.com';

        $format = '  <url><loc>%s</loc>%s'
            . '<xhtml:link rel="alternate" hreflang="en" href="%s"/>'
            . '<xhtml:link rel="alternate" hreflang="pl" href="%s"/>'
            . '<xhtml:link rel="alternate" hreflang="x-default" href="%s"/></url>';

        return sprintf(
            $format,
            $base . $location,
            $this->lastModified($lastModified),
            $base . $english,
            $base . $polish,
            $base . $english
        );
    }

    private function lastModified(?\DateTimeImmutable $lastModified): string
    {
        return $lastModified === null ? '' : '<lastmod>' . $lastModified->format('Y-m-d') . '</lastmod>';
    }

    /** @param list<BlogArticle> $articles */
    private function latestUpdate(array $articles): ?\DateTimeImmutable
    {
        $latest = null;

        foreach ($articles as $article) {
            if ($latest === null || $article->getUpdatedAt() > $latest) {
                $latest = $article->getUpdatedAt();
            }
        }

        return $latest;
    }
}
