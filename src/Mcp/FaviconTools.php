<?php

declare(strict_types=1);

namespace App\Mcp;

use App\FaviconSuite\Application\FaviconSuiteService;
use Mcp\Capability\Attribute\McpTool;
use Mcp\Capability\Attribute\Schema;

final readonly class FaviconTools
{
    public function __construct(private FaviconSuiteService $faviconSuiteService)
    {
    }

    #[McpTool(
        name: 'generate_favicon_suite',
        // phpcs:ignore Generic.Files.LineLength
        description: 'Generate modern multi-platform favicon bundle (adaptive dark-mode SVG, multi-resolution ICO, Apple Touch Icon, Android PWA icons, webmanifest) and minimal HTML tags from SVG or image input.'
    )]
    public function generateFaviconSuite(
        #[Schema(description: 'The raw SVG XML markup string to convert into modern multi-platform favicon suite.')]
        string $svg_content,
        #[Schema(description: 'Optional dark mode strategy: "css_invert_fill" (default), "css_class_swap", or "preserve_colors".')]
        ?string $dark_mode_strategy = 'css_invert_fill',
    ): string {
        try {
            $result = $this->faviconSuiteService->generate(
                svgContent: $svg_content,
                darkModeStrategy: $dark_mode_strategy,
            );

            return $this->json([
                'status' => 'completed',
                'result' => $result->toArray(),
            ]);
        } catch (\Throwable $e) {
            return $this->json([
                'status' => 'error',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    private function json(array $data): string
    {
        return json_encode(
            $data,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        );
    }
}
