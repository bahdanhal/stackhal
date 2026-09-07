<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Crawl\Domain\RobotsPolicy;
use PHPUnit\Framework\TestCase;

final class RobotsPolicyTest extends TestCase
{
    public function testAppliesAllowDisallowAndCrawlDelay(): void
    {
        $parser = new RobotsPolicy();
        $robotsTxt = implode("\n", [
            'User-agent: *',
            'Disallow: /private/',
            'Allow: /private/public',
            'Disallow: /*?preview=*$',
            'Crawl-delay: 2',
        ]);
        $policy = $parser->parse($robotsTxt);

        self::assertFalse($parser->allows('https://example.com/private/report', $policy));
        self::assertTrue($parser->allows('https://example.com/private/public', $policy));
        self::assertTrue($parser->allows('https://example.com/articles', $policy));
        self::assertFalse($parser->allows('https://example.com/article?preview=yes', $policy));
        self::assertSame(2000, $policy['crawl_delay_ms']);
    }

    public function testStackhalRobotsTxtAllowsPolishAuditLandingPagesAndBlocksPostEndpoints(): void
    {
        $parser = new RobotsPolicy();
        $robotsTxt = (string) file_get_contents(dirname(__DIR__, 2) . '/public/robots.txt');
        $policy = $parser->parse($robotsTxt);

        self::assertTrue($parser->allows('https://stackhal.com/', $policy));
        self::assertTrue($parser->allows('https://stackhal.com/geo-audit', $policy));
        self::assertTrue($parser->allows('https://stackhal.com/seo-audit', $policy));
        self::assertTrue($parser->allows('https://stackhal.com/pl/audyt-geo', $policy));
        self::assertTrue($parser->allows('https://stackhal.com/pl/audyt-seo', $policy));

        self::assertFalse($parser->allows('https://stackhal.com/audit', $policy));
        self::assertFalse($parser->allows('https://stackhal.com/pl/audyt', $policy));
        self::assertFalse($parser->allows('https://stackhal.com/contact', $policy));
        self::assertFalse($parser->allows('https://stackhal.com/pl/kontakt', $policy));
        self::assertFalse($parser->allows('https://stackhal.com/api/v1/pkpass/validate', $policy));
        self::assertFalse($parser->allows('https://stackhal.com/admin/login', $policy));
    }
}
