<?php

declare(strict_types=1);

namespace App\Tests\Mcp;

use App\FaviconSuite\Application\FaviconSuiteService;
use App\Mcp\FaviconTools;
use PHPUnit\Framework\TestCase;

final class FaviconToolsTest extends TestCase
{
    private FaviconTools $tools;

    protected function setUp(): void
    {
        $this->tools = new FaviconTools(new FaviconSuiteService());
    }

    public function testGenerateFaviconSuiteToolSuccess(): void
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" fill="#111827"/></svg>';
        $response = $this->tools->generateFaviconSuite($svg, 'css_invert_fill');

        $data = json_decode($response, true, flags: JSON_THROW_ON_ERROR);
        self::assertSame('completed', $data['status']);
        self::assertTrue($data['result']['valid']);
        self::assertTrue($data['result']['dark_mode_injected']);
        self::assertNotEmpty($data['result']['files']);
        self::assertNotEmpty($data['result']['html_tags']);
    }

    public function testGenerateFaviconSuiteToolMalformedSvg(): void
    {
        $svg = '<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="40"';
        $response = $this->tools->generateFaviconSuite($svg);

        $data = json_decode($response, true, flags: JSON_THROW_ON_ERROR);
        self::assertSame('completed', $data['status']);
        self::assertFalse($data['result']['valid']);
        self::assertNotEmpty($data['result']['diagnostics']);
    }
}
