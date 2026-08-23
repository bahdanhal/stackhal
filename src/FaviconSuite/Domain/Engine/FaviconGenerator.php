<?php

declare(strict_types=1);

namespace App\FaviconSuite\Domain\Engine;

use App\FaviconSuite\Domain\Model\DarkModeStrategy;
use App\FaviconSuite\Domain\Model\FaviconBundleFile;
use App\FaviconSuite\Domain\Model\FaviconDiagnostic;
use App\FaviconSuite\Domain\Model\FaviconSuiteResult;

final class FaviconGenerator
{
    private const array DIAGNOSTIC_DEFINITIONS = [
        'ERR_INVALID_IMAGE_FORMAT' => [
            'severity' => 'error',
            'title' => 'Invalid Image Format',
            'description' => 'Uploaded file is not a recognized SVG, PNG, WebP, or JPEG image.',
        ],
        'ERR_MALFORMED_SVG' => [
            'severity' => 'error',
            'title' => 'Malformed SVG Document',
            'description' => 'The SVG content could not be parsed as valid XML.',
        ],
        'WARN_LOW_RESOLUTION_SOURCE' => [
            'severity' => 'warning',
            'title' => 'Low Resolution Source Image',
            'description' => 'Source raster image is smaller than 512x512 pixels; generated PWA and Retina icons may appear pixelated.',
        ],
        'WARN_MISSING_VIEWBOX' => [
            'severity' => 'warning',
            'title' => 'Missing viewBox in SVG',
            'description' => 'SVG lacks a viewBox attribute; automatic scaling across browser chrome may be distorted.',
        ],
        'WARN_NON_SQUARE_ASPECT' => [
            'severity' => 'warning',
            'title' => 'Non-Square Aspect Ratio',
            'description' => 'Image is not 1:1 square. Icon will be centered with padding to prevent distortion.',
        ],
        'INFO_DARK_MODE_INJECTED' => [
            'severity' => 'info',
            'title' => 'Dark-Mode CSS Injected',
            'description' => 'Added prefers-color-scheme dark styling to ensure visibility on dark browser tab bars.',
        ],
        'INFO_MASKABLE_SAFE_ZONE' => [
            'severity' => 'info',
            'title' => 'Adaptive Mask Safe-Zone Applied',
            'description' => 'Scaled icon inside 80% inner core with 10% outer padding for Android maskable icon.',
        ],
    ];

    /**
     * @param array{width?: int, height?: int}|null $rasterMetadata
     */
    public function generateFromSvg(
        string $svgContent,
        DarkModeStrategy $strategy = DarkModeStrategy::CSS_INVERT_FILL,
        ?array $rasterMetadata = null,
    ): FaviconSuiteResult {
        $trimmed = trim($svgContent);
        if ($trimmed === '' && $rasterMetadata !== null) {
            return $this->generateFromRasterMetadata($rasterMetadata);
        }

        if (!str_contains($trimmed, '<svg')) {
            return new FaviconSuiteResult(
                isValid: false,
                files: [],
                htmlTags: [],
                diagnostics: [$this->createDiagnostic('ERR_INVALID_IMAGE_FORMAT')],
                darkModeInjected: false,
            );
        }

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $loaded = $dom->loadXML($trimmed, LIBXML_NONET | LIBXML_NOWARNING | LIBXML_NOERROR);
        $errors = libxml_get_errors();
        libxml_clear_errors();

        if (!$loaded || !empty($errors)) {
            return new FaviconSuiteResult(
                isValid: false,
                files: [],
                htmlTags: [],
                diagnostics: [$this->createDiagnostic('ERR_MALFORMED_SVG')],
                darkModeInjected: false,
            );
        }

        /** @var \DOMElement|null $svgElement */
        $svgElement = $dom->getElementsByTagName('svg')->item(0);
        if ($svgElement === null) {
            return new FaviconSuiteResult(
                isValid: false,
                files: [],
                htmlTags: [],
                diagnostics: [$this->createDiagnostic('ERR_MALFORMED_SVG')],
                darkModeInjected: false,
            );
        }

        /** @var list<FaviconDiagnostic> $diagnostics */
        $diagnostics = [];

        $hasViewBox = $svgElement->hasAttribute('viewBox');
        if (!$hasViewBox) {
            $diagnostics[] = $this->createDiagnostic('WARN_MISSING_VIEWBOX');
        }

        $width = $svgElement->getAttribute('width');
        $height = $svgElement->getAttribute('height');
        $viewBox = $svgElement->getAttribute('viewBox');

        $isSquare = true;
        if ($viewBox !== '') {
            $parts = preg_split('/[\s,]+/', trim($viewBox));
            if ($parts !== false && count($parts) === 4) {
                $vbW = (float) $parts[2];
                $vbH = (float) $parts[3];
                if ($vbW > 0 && $vbH > 0 && abs($vbW - $vbH) > 0.001) {
                    $isSquare = false;
                }
            }
        } elseif ($width !== '' && $height !== '') {
            $w = (float) $width;
            $h = (float) $height;
            if ($w > 0 && $h > 0 && abs($w - $h) > 0.001) {
                $isSquare = false;
            }
        }

        if (!$isSquare) {
            $diagnostics[] = $this->createDiagnostic('WARN_NON_SQUARE_ASPECT');
        }

        $darkModeInjected = false;
        $processedSvg = $trimmed;

        if ($strategy !== DarkModeStrategy::PRESERVE_COLORS) {
            $hasPrefersDark = str_contains($trimmed, 'prefers-color-scheme') || str_contains($trimmed, 'prefers-color-scheme: dark');
            if (!$hasPrefersDark) {
                $cssStyle = match ($strategy) {
                    // phpcs:ignore Generic.Files.LineLength
                    DarkModeStrategy::CSS_INVERT_FILL => '@media (prefers-color-scheme: dark) { :root { filter: invert(1) hue-rotate(180deg); } circle, path, rect, polygon, ellipse { fill: #ffffff !important; } }',
                    // phpcs:ignore Generic.Files.LineLength
                    DarkModeStrategy::CSS_CLASS_SWAP => '@media (prefers-color-scheme: dark) { .light-theme { display: none; } .dark-theme { display: block; } }',
                };

                $styleElement = $dom->createElement('style', $cssStyle);
                $firstNode = $svgElement->firstChild;
                if ($firstNode !== null) {
                    $svgElement->insertBefore($styleElement, $firstNode);
                } else {
                    $svgElement->appendChild($styleElement);
                }

                $savedXml = $dom->saveXML();
                if ($savedXml !== false) {
                    $processedSvg = $savedXml;
                    $darkModeInjected = true;
                    $diagnostics[] = $this->createDiagnostic('INFO_DARK_MODE_INJECTED');
                }
            }
        }

        $diagnostics[] = $this->createDiagnostic('INFO_MASKABLE_SAFE_ZONE');

        $files = $this->buildBundleFiles($processedSvg);
        $htmlTags = $this->getRecommendedHtmlTags();
        $manifestContent = $this->generateWebManifest();

        return new FaviconSuiteResult(
            isValid: true,
            files: $files,
            htmlTags: $htmlTags,
            diagnostics: $diagnostics,
            darkModeInjected: $darkModeInjected,
            svgContent: $processedSvg,
            manifestContent: $manifestContent,
        );
    }

    /**
     * @param array{width?: int, height?: int} $metadata
     */
    public function generateFromRasterMetadata(array $metadata): FaviconSuiteResult
    {
        /** @var list<FaviconDiagnostic> $diagnostics */
        $diagnostics = [];
        $width = $metadata['width'] ?? 0;
        $height = $metadata['height'] ?? 0;

        if ($width > 0 && $height > 0 && $width !== $height) {
            $diagnostics[] = $this->createDiagnostic('WARN_NON_SQUARE_ASPECT');
        }

        if ($width > 0 && $height > 0 && ($width < 512 || $height < 512)) {
            $diagnostics[] = $this->createDiagnostic('WARN_LOW_RESOLUTION_SOURCE');
        }

        $diagnostics[] = $this->createDiagnostic('INFO_MASKABLE_SAFE_ZONE');

        $files = $this->buildBundleFiles(null);
        $htmlTags = $this->getRecommendedHtmlTags();
        $manifestContent = $this->generateWebManifest();

        return new FaviconSuiteResult(
            isValid: true,
            files: $files,
            htmlTags: $htmlTags,
            diagnostics: $diagnostics,
            darkModeInjected: false,
            svgContent: null,
            manifestContent: $manifestContent,
        );
    }

    /**
     * @return list<string>
     */
    public function getRecommendedHtmlTags(): array
    {
        return [
            '<link rel="icon" href="/favicon.ico" sizes="32x32">',
            '<link rel="icon" href="/favicon.svg" type="image/svg+xml">',
            '<link rel="apple-touch-icon" href="/apple-touch-icon.png">',
            '<link rel="manifest" href="/site.webmanifest">',
        ];
    }

    /**
     * @return list<FaviconBundleFile>
     */
    private function buildBundleFiles(?string $svgContent): array
    {
        $manifest = $this->generateWebManifest();

        return [
            new FaviconBundleFile(
                filename: 'favicon.svg',
                mimeType: 'image/svg+xml',
                description: 'Vector favicon supporting light and dark mode via embedded CSS @media (prefers-color-scheme: dark).',
                content: $svgContent,
            ),
            new FaviconBundleFile(
                filename: 'favicon.ico',
                mimeType: 'image/x-icon',
                description: 'Legacy fallback multi-resolution ICO file for legacy desktop browsers and RSS readers.',
                dimensions: [
                    ['width' => 16, 'height' => 16],
                    ['width' => 32, 'height' => 32],
                    ['width' => 48, 'height' => 48],
                ],
            ),
            new FaviconBundleFile(
                filename: 'apple-touch-icon.png',
                mimeType: 'image/png',
                description: 'Apple iOS Home Screen bookmark icon with Apple HIG safe-zone padding.',
                width: 180,
                height: 180,
            ),
            new FaviconBundleFile(
                filename: 'icon-192.png',
                mimeType: 'image/png',
                description: 'Standard Android Chrome PWA home screen icon.',
                width: 192,
                height: 192,
                purpose: 'any',
            ),
            new FaviconBundleFile(
                filename: 'icon-512.png',
                mimeType: 'image/png',
                description: 'High-resolution PWA splash screen icon.',
                width: 512,
                height: 512,
                purpose: 'any',
            ),
            new FaviconBundleFile(
                filename: 'icon-maskable-512.png',
                mimeType: 'image/png',
                description: 'Android Adaptive icon with 10% safe zone boundary to prevent clipping in squircle/circle masks.',
                width: 512,
                height: 512,
                purpose: 'maskable',
                safeZoneInsetRatio: 0.1,
            ),
            new FaviconBundleFile(
                filename: 'site.webmanifest',
                mimeType: 'application/manifest+json',
                description: 'Standard Web App Manifest JSON registering icons and theme color.',
                content: $manifest,
            ),
        ];
    }

    public function generateWebManifest(string $name = 'StackHal App', string $themeColor = '#ffffff'): string
    {
        $data = [
            'name' => $name,
            'short_name' => $name,
            'icons' => [
                [
                    'src' => '/icon-192.png',
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'any',
                ],
                [
                    'src' => '/icon-512.png',
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'any',
                ],
                [
                    'src' => '/icon-maskable-512.png',
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'maskable',
                ],
            ],
            'theme_color' => $themeColor,
            'background_color' => $themeColor,
            'display' => 'standalone',
        ];

        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }

    private function createDiagnostic(string $code): FaviconDiagnostic
    {
        $def = self::DIAGNOSTIC_DEFINITIONS[$code] ?? [
            'severity' => 'info',
            'title' => $code,
            'description' => $code,
        ];

        return new FaviconDiagnostic(
            code: $code,
            severity: $def['severity'],
            title: $def['title'],
            description: $def['description'],
        );
    }
}
