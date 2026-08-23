<?php

declare(strict_types=1);

namespace App\Tests\FaviconSuite;

use App\FaviconSuite\Application\FaviconSuiteService;
use App\FaviconSuite\Domain\Engine\FaviconGenerator;
use App\FaviconSuite\Domain\Model\DarkModeStrategy;
use PHPUnit\Framework\TestCase;

final class FaviconSuiteGeneratorTest extends TestCase
{
    private FaviconGenerator $generator;
    private FaviconSuiteService $service;

    protected function setUp(): void
    {
        $this->generator = new FaviconGenerator();
        $this->service = new FaviconSuiteService($this->generator);
    }

    public function testValidMonochromeSvgGeneration(): void
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" fill="#111827"/></svg>';
        $result = $this->service->generate($svg, 'css_invert_fill');

        self::assertTrue($result->isValid);
        self::assertTrue($result->darkModeInjected);
        self::assertNotEmpty($result->files);
        self::assertNotEmpty($result->htmlTags);
        self::assertNotNull($result->manifestContent);
        self::assertStringContainsString('site.webmanifest', $result->htmlTags[3]);
        self::assertContains('INFO_DARK_MODE_INJECTED', array_map(static fn ($d) => $d->code, $result->diagnostics));
    }

    public function testMalformedSvgRejection(): void
    {
        $svg = '<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="40"';
        $result = $this->generator->generateFromSvg($svg);

        self::assertFalse($result->isValid);
        self::assertContains('ERR_MALFORMED_SVG', $result->getErrorCodes());
    }

    public function testInvalidFormatRejection(): void
    {
        $nonSvg = 'This is plain text and definitely not an SVG';
        $result = $this->generator->generateFromSvg($nonSvg);

        self::assertFalse($result->isValid);
        self::assertContains('ERR_INVALID_IMAGE_FORMAT', $result->getErrorCodes());
    }

    public function testRasterMetadataValidation(): void
    {
        $result = $this->generator->generateFromRasterMetadata(['width' => 600, 'height' => 400]);

        self::assertTrue($result->isValid);
        self::assertContains('WARN_NON_SQUARE_ASPECT', $result->getWarningCodes());
    }

    public function testLowResolutionRasterWarning(): void
    {
        $result = $this->generator->generateFromRasterMetadata(['width' => 128, 'height' => 128]);

        self::assertTrue($result->isValid);
        self::assertContains('WARN_LOW_RESOLUTION_SOURCE', $result->getWarningCodes());
    }

    public function testPresetsAvailable(): void
    {
        $presets = $this->service->getPresets();
        self::assertCount(3, $presets);
        self::assertSame('monochrome_inverting_logo', $presets[0]['id']);
    }

    public function testPreserveColorsStrategy(): void
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" fill="#111827"/></svg>';
        $result = $this->service->generate($svg, DarkModeStrategy::PRESERVE_COLORS->value);

        self::assertTrue($result->isValid);
        self::assertFalse($result->darkModeInjected);
    }
}
