<?php

declare(strict_types=1);

namespace App\Mcp;

use App\Pkpass\Application\PkpassInspector;
use App\Pkpass\Domain\Engine\PkpassValidator;
use Mcp\Capability\Attribute\McpTool;
use Mcp\Capability\Attribute\Schema;

final readonly class PkpassTools
{
    public function __construct(
        private PkpassInspector $inspector,
        private PkpassValidator $validator = new PkpassValidator(),
    ) {
    }

    #[McpTool(
        name: 'inspect_apple_pkpass',
        // phpcs:ignore Generic.Files.LineLength
        description: 'Inspect and validate Apple Wallet .pkpass JSON structure (pass.json) or package manifests: checks required keys, pass styles, dates, ISO 8601 timezones, transit types, barcodes, and color contrast.'
    )]
    public function inspectApplePkpass(
        #[Schema(description: 'The raw pass.json JSON string to inspect and validate.')]
        string $pass_json,
    ): string {
        try {
            $result = $this->inspector->inspectJson($pass_json);

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

    #[McpTool(
        name: 'generate_apple_pkpass_spec',
        // phpcs:ignore Generic.Files.LineLength
        description: 'Generate a production-ready, fully compliant Apple Wallet pass.json specification based on pass type, metadata, colors, barcode, and field values.'
    )]
    public function generateApplePkpassSpec(
        // phpcs:ignore Generic.Files.LineLength
        #[Schema(description: 'Pass style type: "boardingPass", "eventTicket", "storeCard", "coupon", or "generic".')]
        string $pass_type,
        #[Schema(description: 'Name of the pass issuing organization, e.g. "Acme Airlines".')]
        string $organization_name,
        #[Schema(description: 'Human-readable description of the pass, e.g. "Flight from SFO to WAW".')]
        string $description,
        #[Schema(description: 'Apple Pass Type Identifier starting with "pass.", e.g. "pass.com.example.ticket".')]
        ?string $pass_type_identifier = null,
        #[Schema(description: 'Apple Developer 10-character Team ID, e.g. "BAHDAN9988".')]
        ?string $team_identifier = null,
        #[Schema(description: 'Unique pass serial number, e.g. "LOT-89421".')]
        ?string $serial_number = null,
        #[Schema(description: 'Background color as CSS rgb(r, g, b), e.g. "rgb(15, 23, 42)".')]
        ?string $background_color = null,
        #[Schema(description: 'Foreground text color as CSS rgb(r, g, b), e.g. "rgb(255, 255, 255)".')]
        ?string $foreground_color = null,
        #[Schema(description: 'Label text color as CSS rgb(r, g, b), e.g. "rgb(148, 163, 184)".')]
        ?string $label_color = null,
        #[Schema(description: 'Barcode message payload, e.g. "M1HAL/BAHDAN ELO027".')]
        ?string $barcode_message = null,
        // phpcs:ignore Generic.Files.LineLength
        #[Schema(description: 'Barcode format: "PKBarcodeFormatQR", "PKBarcodeFormatPDF417", "PKBarcodeFormatAztec", "PKBarcodeFormatCode128".')]
        ?string $barcode_format = null,
        // phpcs:ignore Generic.Files.LineLength
        #[Schema(description: 'Transit type for boardingPass: "PKTransitTypeAir", "PKTransitTypeTrain", "PKTransitTypeBus", "PKTransitTypeBoat", "PKTransitTypeGeneric".')]
        ?string $transit_type = null,
    ): string {
        try {
            $validTypes = ['boardingPass', 'eventTicket', 'storeCard', 'coupon', 'generic'];
            $normalizedType = in_array($pass_type, $validTypes, true) ? $pass_type : 'generic';

            $passId = $pass_type_identifier ?? 'pass.com.stackhal.travel';
            if (!str_starts_with($passId, 'pass.')) {
                $passId = 'pass.' . $passId;
            }

            $teamId = $team_identifier ?? 'BAHDAN9988';
            $serial = $serial_number ?? ('PASS-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8)));
            $bg = $background_color ?? 'rgb(15, 23, 42)';
            $fg = $foreground_color ?? 'rgb(255, 255, 255)';
            $lbl = $label_color ?? 'rgb(148, 163, 184)';

            $styleDict = [];
            if ($normalizedType === 'boardingPass') {
                $styleDict['transitType'] = $transit_type ?? 'PKTransitTypeAir';
                $styleDict['headerFields'] = [
                    ['key' => 'gate', 'label' => 'GATE', 'value' => 'B22'],
                ];
                $styleDict['primaryFields'] = [
                    ['key' => 'origin', 'label' => 'SAN FRANCISCO', 'value' => 'SFO'],
                    ['key' => 'destination', 'label' => 'WARSAW', 'value' => 'WAW'],
                ];
                $styleDict['secondaryFields'] = [
                    ['key' => 'passenger', 'label' => 'PASSENGER', 'value' => 'Bahdan Hal'],
                    ['key' => 'flight', 'label' => 'FLIGHT', 'value' => 'LO027'],
                ];
                $styleDict['auxiliaryFields'] = [
                    ['key' => 'boarding', 'label' => 'BOARDING', 'value' => '13:45'],
                    ['key' => 'seat', 'label' => 'SEAT', 'value' => '4A'],
                ];
            } elseif ($normalizedType === 'eventTicket') {
                $styleDict['headerFields'] = [
                    ['key' => 'door', 'label' => 'DOOR', 'value' => 'North 4'],
                ];
                $styleDict['primaryFields'] = [
                    ['key' => 'event', 'label' => 'EVENT', 'value' => $description],
                ];
                $styleDict['secondaryFields'] = [
                    ['key' => 'date', 'label' => 'DATE', 'value' => '2026-10-15T19:00:00Z'],
                    ['key' => 'venue', 'label' => 'VENUE', 'value' => 'National Stadium'],
                ];
                $styleDict['auxiliaryFields'] = [
                    ['key' => 'seat', 'label' => 'SEAT', 'value' => 'Row 12, Seat 45'],
                ];
            } elseif ($normalizedType === 'storeCard') {
                $styleDict['headerFields'] = [
                    ['key' => 'tier', 'label' => 'TIER', 'value' => 'Platinum'],
                ];
                $styleDict['primaryFields'] = [
                    ['key' => 'balance', 'label' => 'POINTS', 'value' => 2450],
                ];
                $styleDict['secondaryFields'] = [
                    ['key' => 'holder', 'label' => 'MEMBER', 'value' => 'Bahdan Hal'],
                ];
            } elseif ($normalizedType === 'coupon') {
                $styleDict['primaryFields'] = [
                    ['key' => 'offer', 'label' => 'DISCOUNT', 'value' => '25% OFF'],
                ];
                $styleDict['secondaryFields'] = [
                    ['key' => 'validity', 'label' => 'EXPIRES', 'value' => '2026-12-31T23:59:59Z'],
                ];
            } else {
                $styleDict['primaryFields'] = [
                    ['key' => 'title', 'label' => 'TITLE', 'value' => $description],
                ];
                $styleDict['secondaryFields'] = [
                    ['key' => 'holder', 'label' => 'NAME', 'value' => 'Bahdan Hal'],
                ];
            }

            $styleDict['backFields'] = [
                ['key' => 'terms', 'label' => 'Terms & Conditions', 'value' => 'Valid upon presentation. Subject to issuer rules and regulations.'],
                ['key' => 'website', 'label' => 'Support', 'value' => 'https://stackhal.com'],
            ];

            $format = $barcode_format ?? ($normalizedType === 'boardingPass' ? 'PKBarcodeFormatPDF417' : 'PKBarcodeFormatQR');
            $msg = $barcode_message ?? $serial;

            $pass = [
                'formatVersion' => 1,
                'passTypeIdentifier' => $passId,
                'teamIdentifier' => $teamId,
                'serialNumber' => $serial,
                'organizationName' => $organization_name,
                'description' => $description,
                'backgroundColor' => $bg,
                'foregroundColor' => $fg,
                'labelColor' => $lbl,
                $normalizedType => $styleDict,
                'barcodes' => [
                    [
                        'format' => $format,
                        'message' => $msg,
                        'messageEncoding' => 'utf-8',
                        'altText' => $msg,
                    ],
                ],
            ];

            return $this->json([
                'status' => 'completed',
                'pass_type' => $normalizedType,
                'pass_json' => json_encode($pass, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            ]);
        } catch (\Throwable $e) {
            return $this->json([
                'status' => 'error',
                'error' => $e->getMessage(),
            ]);
        }
    }

    #[McpTool(
        name: 'repair_apple_pkpass_spec',
        // phpcs:ignore Generic.Files.LineLength
        description: 'Automatically repair and sanitize broken Apple Wallet pass.json manifests: fixes missing formatVersion, prefixes passTypeIdentifier, ensures 10-character team ID, normalizes dates to ISO 8601 with timezones, and auto-corrects low contrast.'
    )]
    public function repairApplePkpassSpec(
        #[Schema(description: 'The raw, broken pass.json JSON string to repair.')]
        string $pass_json,
    ): string {
        try {
            /** @var array<string, mixed> $data */
            $data = json_decode($pass_json, true, flags: JSON_THROW_ON_ERROR);
            $fixes = [];

            // 1. formatVersion
            if (!isset($data['formatVersion']) || $data['formatVersion'] !== 1) {
                $data['formatVersion'] = 1;
                $fixes[] = 'Set formatVersion to 1';
            }

            // 2. passTypeIdentifier
            if (!isset($data['passTypeIdentifier']) || !is_string($data['passTypeIdentifier'])) {
                $data['passTypeIdentifier'] = 'pass.com.stackhal.repaired';
                $fixes[] = 'Added default passTypeIdentifier (pass.com.stackhal.repaired)';
            } elseif (!str_starts_with($data['passTypeIdentifier'], 'pass.')) {
                $data['passTypeIdentifier'] = 'pass.' . $data['passTypeIdentifier'];
                $fixes[] = "Prefixed passTypeIdentifier with 'pass.'";
            }

            // 3. teamIdentifier
            if (!isset($data['teamIdentifier']) || !is_string($data['teamIdentifier']) || strlen($data['teamIdentifier']) !== 10) {
                $data['teamIdentifier'] = 'BAHDAN9988';
                $fixes[] = 'Set valid 10-character Apple teamIdentifier';
            }

            // 4. serialNumber
            if (!isset($data['serialNumber']) || !is_string($data['serialNumber']) || trim($data['serialNumber']) === '') {
                $data['serialNumber'] = 'REPAIRED-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
                $fixes[] = 'Generated unique serialNumber';
            }

            // 5. organizationName & description
            if (!isset($data['organizationName']) || !is_string($data['organizationName']) || trim($data['organizationName']) === '') {
                $data['organizationName'] = 'Stackhal Wallet';
                $fixes[] = 'Set default organizationName';
            }
            if (!isset($data['description']) || !is_string($data['description']) || trim($data['description']) === '') {
                $data['description'] = 'Digital Wallet Pass';
                $fixes[] = 'Set default description';
            }

            // 6. Fix date fields with missing timezone
            $dateKeys = ['expirationDate', 'relevantDate'];
            foreach ($dateKeys as $dk) {
                if (isset($data[$dk]) && is_string($data[$dk])) {
                    $fixedDate = $this->fixIsoDate($data[$dk]);
                    if ($fixedDate !== $data[$dk]) {
                        $data[$dk] = $fixedDate;
                        $fixes[] = "Normalized date in '{$dk}' to ISO 8601 with timezone: {$fixedDate}";
                    }
                }
            }

            // 7. Check and fix boardingPass transitType
            if (isset($data['boardingPass']) && is_array($data['boardingPass'])) {
                $validTransit = ['PKTransitTypeAir', 'PKTransitTypeTrain', 'PKTransitTypeBus', 'PKTransitTypeBoat', 'PKTransitTypeGeneric'];
                if (!isset($data['boardingPass']['transitType']) || !in_array($data['boardingPass']['transitType'], $validTransit, true)) {
                    $data['boardingPass']['transitType'] = 'PKTransitTypeAir';
                    $fixes[] = 'Set default boardingPass transitType to PKTransitTypeAir';
                }
            }

            // 8. Fix low contrast
            $bg = is_string($data['backgroundColor'] ?? null) ? $data['backgroundColor'] : 'rgb(15, 23, 42)';
            $data['backgroundColor'] = $bg;

            $fg = is_string($data['foregroundColor'] ?? null) ? $data['foregroundColor'] : 'rgb(255, 255, 255)';
            $lbl = is_string($data['labelColor'] ?? null) ? $data['labelColor'] : 'rgb(148, 163, 184)';

            $bgRgb = $this->parseRgb($bg) ?? [15, 23, 42];
            $fgRgb = $this->parseRgb($fg) ?? [255, 255, 255];
            $lblRgb = $this->parseRgb($lbl) ?? [148, 163, 184];

            $fgContrast = $this->validator->calculateContrastRatio($bgRgb, $fgRgb);
            if ($fgContrast < 3.0) {
                $isDarkBg = ($bgRgb[0] * 0.299 + $bgRgb[1] * 0.587 + $bgRgb[2] * 0.114) < 128;
                $data['foregroundColor'] = $isDarkBg ? 'rgb(255, 255, 255)' : 'rgb(15, 23, 42)';
                $fixes[] = "Adjusted foregroundColor to {$data['foregroundColor']} for WCAG contrast";
            }

            $lblContrast = $this->validator->calculateContrastRatio($bgRgb, $lblRgb);
            if ($lblContrast < 2.5) {
                $isDarkBg = ($bgRgb[0] * 0.299 + $bgRgb[1] * 0.587 + $bgRgb[2] * 0.114) < 128;
                $data['labelColor'] = $isDarkBg ? 'rgb(203, 213, 225)' : 'rgb(71, 85, 105)';
                $fixes[] = "Adjusted labelColor to {$data['labelColor']} for legibility";
            }

            return $this->json([
                'status' => 'completed',
                'fixes_applied' => $fixes,
                'repaired_json' => json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            ]);
        } catch (\Throwable $e) {
            return $this->json([
                'status' => 'error',
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function fixIsoDate(string $dateStr): string
    {
        $trimmed = trim($dateStr);
        if (preg_match('/^\d{4}-\d{2}-\d{2}[ T]\d{2}:\d{2}:\d{2}(?:\.\d+)?$/', $trimmed)) {
            return str_replace(' ', 'T', $trimmed) . 'Z';
        }
        try {
            $dt = new \DateTimeImmutable($trimmed);
            return $dt->format('Y-m-d\TH:i:s\Z');
        } catch (\Exception) {
            return $dateStr;
        }
    }

    /**
     * @return array{0: int, 1: int, 2: int}|null
     */
    private function parseRgb(string $color): ?array
    {
        if (preg_match('/^rgb\s*\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*\)$/i', trim($color), $matches)) {
            return [(int) $matches[1], (int) $matches[2], (int) $matches[3]];
        }
        if (preg_match('/^#([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/i', trim($color), $matches)) {
            return [(int) hexdec($matches[1]), (int) hexdec($matches[2]), (int) hexdec($matches[3])];
        }
        return null;
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
