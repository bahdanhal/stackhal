<?php

declare(strict_types=1);

namespace App\Shared\Presentation\Http;

use App\Blog\Application\BlogArticleRepository;
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
            ['/composer-license-checker', '/pl/sprawdzanie-licencji-composer'],
            ['/blog', '/blog'],
        ];

        if ($articles !== null) {
            foreach ($articles->findPublished() as $article) {
                $pairs[] = ['/blog/' . $article->getSlug(), '/blog/' . $article->getSlug()];
            }
        }

        $entries = [];
        foreach ($pairs as [$en, $pl]) {
            $entries[] = $this->entry($en, $en, $pl);
            $entries[] = $this->entry($pl, $en, $pl);
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

    private function entry(string $location, string $english, string $polish): string
    {
        $base = 'https://stackhal.com';

        $format = '  <url><loc>%s</loc>'
            . '<xhtml:link rel="alternate" hreflang="en" href="%s"/>'
            . '<xhtml:link rel="alternate" hreflang="pl" href="%s"/>'
            . '<xhtml:link rel="alternate" hreflang="x-default" href="%s"/></url>';

        return sprintf(
            $format,
            $base . $location,
            $base . $english,
            $base . $polish,
            $base . $english
        );
    }
}
