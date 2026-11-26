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
        $paths = [
            '/',
            '/geo-audit',
            '/seo-audit',
            '/apple-pkpass-inspector',
            '/ai-studio-local-file-sync',
            '/agent-conversations',
        ];

        $entries = [];
        foreach ($paths as $path) {
            $entries[] = $this->entry($path);
        }

        $publishedEnglish = $articles !== null ? $articles->findPublished('en') : [];

        $entries[] = $this->entry('/blog', $this->latestUpdate($publishedEnglish));

        foreach ($publishedEnglish as $article) {
            $entries[] = $this->entry('/blog/' . $article->getSlug(), $article->getUpdatedAt());
        }

        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
            . "<?xml-stylesheet type=\"text/xsl\" href=\"/sitemap.xsl\"?>\n"
            . "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n"
            . implode("\n", $entries) . "\n"
            . "</urlset>\n";

        return new Response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=300, must-revalidate',
        ]);
    }

    private function entry(string $location, ?\DateTimeImmutable $lastModified = null): string
    {
        return sprintf(
            '  <url><loc>https://stackhal.com%s</loc>%s</url>',
            $location,
            $this->lastModified($lastModified)
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
