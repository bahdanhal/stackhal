<?php

declare(strict_types=1);

namespace App\CidrMatrix\Application;

use App\CidrMatrix\Domain\Engine\CidrCalculator;
use App\CidrMatrix\Domain\Model\CidrBlock;
use App\CidrMatrix\Domain\Model\CidrMatrixResult;

final readonly class CidrMatrixService
{
    private CidrCalculator $calculator;

    public function __construct(?CidrCalculator $calculator = null)
    {
        $this->calculator = $calculator ?? new CidrCalculator();
    }

    /**
     * @param list<string> $cidrs
     */
    public function analyze(
        array $cidrs,
        ?int $requestedFreePrefix = null,
        ?string $parentCidr = null,
    ): CidrMatrixResult {
        return $this->calculator->analyze($cidrs, $requestedFreePrefix, $parentCidr);
    }

    /**
     * Extract valid IPv4 and IPv6 CIDRs from prose, JSON, YAML, Terraform, and route-table output.
     *
     * @return list<string>
     */
    public function extractCidrs(string $input): array
    {
        preg_match_all('/[0-9A-Fa-f:.]+\/\d{1,3}/', $input, $matches);
        $cidrs = [];
        foreach ($matches[0] as $candidate) {
            if (CidrBlock::parse($candidate) !== null && !in_array($candidate, $cidrs, true)) {
                $cidrs[] = $candidate;
            }
        }

        return $cidrs;
    }

    /**
     * @param list<string> $cidrs
     */
    public function suggestFreePrefix(array $cidrs): ?int
    {
        $blocks = array_values(array_filter(array_map(CidrBlock::parse(...), $cidrs)));
        if (count($blocks) < 2) {
            return null;
        }

        $version = $blocks[0]->version;
        foreach ($blocks as $block) {
            if ($block->version !== $version) {
                return null;
            }
        }

        return max(array_map(static fn (CidrBlock $block): int => $block->prefixLength, $blocks));
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getPresets(): array
    {
        $specPath = dirname(__DIR__, 3) . '/specs/cidr-matrix.spec.json';
        if (!file_exists($specPath)) {
            return [];
        }

        $content = file_get_contents($specPath);
        if ($content === false) {
            return [];
        }

        /** @var array{presets?: list<array<string, mixed>>} $spec */
        $spec = json_decode($content, true) ?? [];

        return $spec['presets'] ?? [];
    }
}
