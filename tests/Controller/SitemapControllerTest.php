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
    }
}
