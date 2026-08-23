<?php

declare(strict_types=1);

namespace App\Mcp;

use App\RegexTranspiler\Application\RegexTranspilerService;
use Mcp\Capability\Attribute\McpTool;
use Mcp\Capability\Attribute\Schema;

final readonly class RegexTranspilerTools
{
    public function __construct(private RegexTranspilerService $transpiler)
    {
    }

    #[McpTool(
        name: 'transpile_regex_engine',
        // phpcs:ignore Generic.Files.LineLength
        description: 'Transpile and analyze regular expressions across engines (PCRE, Go RE2, JavaScript, Python re, Rust regex) with compatibility checks and ReDoS safety analysis.'
    )]
    public function transpileRegexEngine(
        #[Schema(description: 'The regular expression pattern string to analyze and transpile.')]
        string $pattern,
        #[Schema(description: 'Optional source engine: "pcre" (default), "go_re2", "javascript", "python", "rust".')]
        ?string $source_engine = 'pcre',
        #[Schema(description: 'Optional target engine: "go_re2" (default), "pcre", "javascript", "python", "rust".')]
        ?string $target_engine = 'go_re2',
    ): string {
        try {
            $result = $this->transpiler->transpile(
                pattern: $pattern,
                sourceEngine: $source_engine,
                targetEngine: $target_engine,
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
