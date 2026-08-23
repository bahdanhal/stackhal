<?php

declare(strict_types=1);

namespace App\FaviconSuite\Application;

use App\FaviconSuite\Domain\Engine\FaviconGenerator;
use App\FaviconSuite\Domain\Model\DarkModeStrategy;
use App\FaviconSuite\Domain\Model\FaviconSuiteResult;

final readonly class FaviconSuiteService
{
    private FaviconGenerator $generator;

    public function __construct(?FaviconGenerator $generator = null)
    {
        $this->generator = $generator ?? new FaviconGenerator();
    }

    /**
     * @param array{width?: int, height?: int}|null $rasterMetadata
     */
    public function generate(
        string $svgContent,
        ?string $darkModeStrategy = null,
        ?array $rasterMetadata = null,
    ): FaviconSuiteResult {
        $strategy = DarkModeStrategy::fromString($darkModeStrategy);

        return $this->generator->generateFromSvg($svgContent, $strategy, $rasterMetadata);
    }

    /**
     * @return list<array{id: string, name: string, description: string, dark_mode_strategy: string, sample_svg: string}>
     */
    public function getPresets(): array
    {
        return [
            [
                'id' => 'monochrome_inverting_logo',
                'name' => 'Monochrome Auto-Invert',
                'description' => 'Black icon in light mode that automatically turns white in dark browser tabs.',
                'dark_mode_strategy' => 'css_invert_fill',
                'sample_svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" fill="#111827"/></svg>',
            ],
            [
                'id' => 'dual_color_brand',
                'name' => 'Dual-Color Brand Mark',
                'description' => 'Custom color palette with alternate dark-mode accent color.',
                'dark_mode_strategy' => 'css_class_swap',
                // phpcs:ignore Generic.Files.LineLength
                'sample_svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect class="light-theme" width="100" height="100" rx="20" fill="#2563eb"/><rect class="dark-theme" width="100" height="100" rx="20" fill="#38bdf8" style="display:none"/><path d="M30 70 L50 30 L70 70 Z" fill="#ffffff"/></svg>',
            ],
            [
                'id' => 'solid_badge_pwa',
                'name' => 'Solid Badge PWA Icon',
                'description' => 'High-contrast geometric badge with rounded background fill for iOS/Android.',
                'dark_mode_strategy' => 'preserve_colors',
                // phpcs:ignore Generic.Files.LineLength
                'sample_svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="46" fill="#0f172a"/><path d="M32 50 L45 63 L68 37" stroke="#38bdf8" stroke-width="8" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg>',
            ],
        ];
    }
}
