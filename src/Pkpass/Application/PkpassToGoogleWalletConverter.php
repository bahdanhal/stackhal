<?php

declare(strict_types=1);

namespace App\Pkpass\Application;

final readonly class PkpassToGoogleWalletConverter
{
    private const string DEFAULT_ISSUER_ID = '3388000000022';

    /**
     * Convert an Apple Wallet pass structure to Google Wallet REST API format.
     *
     * @param array<string, mixed> $pass
     * @param string $issuerId
     * @return array<string, mixed>
     */
    public function convert(array $pass, string $issuerId = self::DEFAULT_ISSUER_ID): array
    {
        $passType = $this->resolvePassType($pass);
        $styleDict = is_array($pass[$passType] ?? null) ? $pass[$passType] : [];

        $sanitizedPassId = preg_replace('/[^a-zA-Z0-9_-]/', '_', (string) ($pass['passTypeIdentifier'] ?? 'pass.sample')) ?? 'pass_sample';
        $sanitizedSerial = preg_replace('/[^a-zA-Z0-9_-]/', '_', (string) ($pass['serialNumber'] ?? 'pass-001')) ?? 'pass_001';

        $classId = sprintf('%s.%s', $issuerId, $sanitizedPassId);
        $objectId = sprintf('%s.%s', $issuerId, $sanitizedSerial);

        $hexColor = $this->parseHexColor($pass['backgroundColor'] ?? null);
        $textModules = $this->buildTextModules($styleDict);
        $barcode = $this->buildBarcode($pass);

        $googleWalletData = match ($passType) {
            'boardingPass' => $this->buildFlightPayload($classId, $objectId, $pass, $styleDict, $textModules, $barcode, $hexColor),
            'eventTicket' => $this->buildEventTicketPayload($classId, $objectId, $pass, $textModules, $barcode, $hexColor),
            'storeCard', 'coupon' => $this->buildLoyaltyPayload($classId, $objectId, $pass, $styleDict, $textModules, $barcode, $hexColor),
            default => $this->buildGenericPayload($classId, $objectId, $pass, $textModules, $barcode, $hexColor),
        };

        $jwtPayload = $this->buildJwtPayload($googleWalletData, $issuerId);
        $encodedPayload = rtrim(strtr(base64_encode((string) json_encode($jwtPayload, JSON_THROW_ON_ERROR)), '+/', '-_'), '=');
        $simulatedJwt = sprintf('eyJhbGciOiJub25lIiwidHlwIjoiSldUIn0.%s.', $encodedPayload);

        return [
            'status' => 'success',
            'passType' => $passType,
            'classId' => $classId,
            'objectId' => $objectId,
            'googleWallet' => $googleWalletData,
            'jwtPayload' => $jwtPayload,
            'jwt' => $simulatedJwt,
            'saveUrl' => 'https://pay.google.com/gp/v/save/' . $simulatedJwt,
        ];
    }

    /**
     * @param array<string, mixed> $pass
     */
    private function resolvePassType(array $pass): string
    {
        $styles = ['boardingPass', 'eventTicket', 'storeCard', 'coupon', 'generic'];
        foreach ($styles as $style) {
            if (isset($pass[$style]) && is_array($pass[$style])) {
                return $style;
            }
        }
        return 'generic';
    }

    /**
     * @param array<string, mixed> $styleDict
     * @return list<array{header: string, body: string, id: string}>
     */
    private function buildTextModules(array $styleDict): array
    {
        $modules = [];
        $groups = ['primaryFields', 'secondaryFields', 'auxiliaryFields', 'headerFields'];

        foreach ($groups as $group) {
            if (!isset($styleDict[$group]) || !is_array($styleDict[$group])) {
                continue;
            }
            foreach ($styleDict[$group] as $field) {
                if (!is_array($field)) {
                    continue;
                }
                $key = is_string($field['key'] ?? null) ? (string) $field['key'] : 'field_' . count($modules);
                $label = is_string($field['label'] ?? null) ? (string) $field['label'] : $key;
                $value = (string) ($field['value'] ?? '');

                $modules[] = [
                    'header' => $label,
                    'body' => $value,
                    'id' => $key,
                ];
            }
        }

        return $modules;
    }

    /**
     * @param array<string, mixed> $pass
     * @return array{type: string, value: string, alternateText: string}|null
     */
    private function buildBarcode(array $pass): ?array
    {
        $barcodes = [];
        if (isset($pass['barcodes']) && is_array($pass['barcodes'])) {
            $barcodes = $pass['barcodes'];
        } elseif (isset($pass['barcode']) && is_array($pass['barcode'])) {
            $barcodes = [$pass['barcode']];
        }

        if (count($barcodes) === 0 || !is_array($barcodes[0])) {
            return null;
        }

        $b = $barcodes[0];
        $typeMap = [
            'PKBarcodeFormatQR' => 'QR_CODE',
            'PKBarcodeFormatPDF417' => 'PDF_417',
            'PKBarcodeFormatAztec' => 'AZTEC',
            'PKBarcodeFormatCode128' => 'CODE_128',
        ];

        $format = is_string($b['format'] ?? null) ? $b['format'] : 'PKBarcodeFormatQR';
        $message = is_string($b['message'] ?? null) ? $b['message'] : '';
        $altText = is_string($b['altText'] ?? null) ? $b['altText'] : '';

        return [
            'type' => $typeMap[$format] ?? 'QR_CODE',
            'value' => $message,
            'alternateText' => $altText,
        ];
    }

    private function parseHexColor(mixed $color): string
    {
        if (!is_string($color)) {
            return '#1e293b';
        }
        $trimmed = trim($color);
        if (preg_match('/^#([0-9a-f]{6})$/i', $trimmed, $m)) {
            return '#' . strtolower($m[1]);
        }
        if (preg_match('/^rgb\s*\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*\)$/i', $trimmed, $m)) {
            return sprintf('#%02x%02x%02x', (int) $m[1], (int) $m[2], (int) $m[3]);
        }
        return '#1e293b';
    }

    /**
     * @param array<string, mixed> $pass
     * @param array<string, mixed> $styleDict
     * @param list<array{header: string, body: string, id: string}> $textModules
     * @param array{type: string, value: string, alternateText: string}|null $barcode
     * @return array<string, mixed>
     */
    private function buildFlightPayload(
        string $classId,
        string $objectId,
        array $pass,
        array $styleDict,
        array $textModules,
        ?array $barcode,
        string $hexColor,
    ): array {
        $passengerName = 'PASSENGER';
        if (isset($styleDict['secondaryFields']) && is_array($styleDict['secondaryFields'])) {
            foreach ($styleDict['secondaryFields'] as $f) {
                if (is_array($f) && ($f['key'] ?? '') === 'passenger') {
                    $passengerName = (string) ($f['value'] ?? 'PASSENGER');
                    break;
                }
            }
        }

        $origin = 'SFO';
        $dest = 'WAW';
        if (isset($styleDict['primaryFields']) && is_array($styleDict['primaryFields'])) {
            if (isset($styleDict['primaryFields'][0]['value'])) {
                $origin = (string) $styleDict['primaryFields'][0]['value'];
            }
            if (isset($styleDict['primaryFields'][1]['value'])) {
                $dest = (string) $styleDict['primaryFields'][1]['value'];
            }
        }

        return [
            'flightClass' => [
                'id' => $classId,
                'issuerName' => (string) ($pass['organizationName'] ?? 'Airline'),
                'reviewStatus' => 'UNDER_REVIEW',
                'origin' => ['airportIataCode' => $origin],
                'destination' => ['airportIataCode' => $dest],
                'hexBackgroundColor' => $hexColor,
            ],
            'flightObject' => [
                'id' => $objectId,
                'classId' => $classId,
                'state' => 'ACTIVE',
                'passengerName' => $passengerName,
                'reservationInfo' => [
                    'confirmationCode' => (string) ($pass['serialNumber'] ?? 'RES123'),
                ],
                'barcode' => $barcode,
                'textModulesData' => $textModules,
                'hexBackgroundColor' => $hexColor,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $pass
     * @param list<array{header: string, body: string, id: string}> $textModules
     * @param array{type: string, value: string, alternateText: string}|null $barcode
     * @return array<string, mixed>
     */
    private function buildEventTicketPayload(
        string $classId,
        string $objectId,
        array $pass,
        array $textModules,
        ?array $barcode,
        string $hexColor,
    ): array {
        return [
            'eventTicketClass' => [
                'id' => $classId,
                'issuerName' => (string) ($pass['organizationName'] ?? 'Event Organizer'),
                'eventName' => [
                    'defaultValue' => [
                        'language' => 'en',
                        'value' => (string) ($pass['description'] ?? 'Event'),
                    ],
                ],
                'reviewStatus' => 'UNDER_REVIEW',
                'hexBackgroundColor' => $hexColor,
            ],
            'eventTicketObject' => [
                'id' => $objectId,
                'classId' => $classId,
                'state' => 'ACTIVE',
                'barcode' => $barcode,
                'textModulesData' => $textModules,
                'hexBackgroundColor' => $hexColor,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $pass
     * @param array<string, mixed> $styleDict
     * @param list<array{header: string, body: string, id: string}> $textModules
     * @param array{type: string, value: string, alternateText: string}|null $barcode
     * @return array<string, mixed>
     */
    private function buildLoyaltyPayload(
        string $classId,
        string $objectId,
        array $pass,
        array $styleDict,
        array $textModules,
        ?array $barcode,
        string $hexColor,
    ): array {
        $accountName = 'Rewards Member';
        if (isset($styleDict['primaryFields'][0]['label'])) {
            $accountName = (string) $styleDict['primaryFields'][0]['label'];
        }

        return [
            'loyaltyClass' => [
                'id' => $classId,
                'issuerName' => (string) ($pass['organizationName'] ?? 'Store'),
                'programName' => (string) ($pass['description'] ?? 'Loyalty Program'),
                'reviewStatus' => 'UNDER_REVIEW',
                'hexBackgroundColor' => $hexColor,
            ],
            'loyaltyObject' => [
                'id' => $objectId,
                'classId' => $classId,
                'state' => 'ACTIVE',
                'accountId' => (string) ($pass['serialNumber'] ?? '12345'),
                'accountName' => $accountName,
                'barcode' => $barcode,
                'textModulesData' => $textModules,
                'hexBackgroundColor' => $hexColor,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $pass
     * @param list<array{header: string, body: string, id: string}> $textModules
     * @param array{type: string, value: string, alternateText: string}|null $barcode
     * @return array<string, mixed>
     */
    private function buildGenericPayload(
        string $classId,
        string $objectId,
        array $pass,
        array $textModules,
        ?array $barcode,
        string $hexColor,
    ): array {
        return [
            'genericClass' => [
                'id' => $classId,
                'reviewStatus' => 'UNDER_REVIEW',
                'hexBackgroundColor' => $hexColor,
            ],
            'genericObject' => [
                'id' => $objectId,
                'classId' => $classId,
                'state' => 'ACTIVE',
                'cardTitle' => [
                    'defaultValue' => [
                        'language' => 'en',
                        'value' => (string) ($pass['organizationName'] ?? 'Pass'),
                    ],
                ],
                'header' => [
                    'defaultValue' => [
                        'language' => 'en',
                        'value' => (string) ($pass['description'] ?? 'Membership'),
                    ],
                ],
                'barcode' => $barcode,
                'textModulesData' => $textModules,
                'hexBackgroundColor' => $hexColor,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $googleWalletData
     * @return array<string, mixed>
     */
    private function buildJwtPayload(array $googleWalletData, string $issuerId): array
    {
        $payload = [];
        if (isset($googleWalletData['flightClass'], $googleWalletData['flightObject'])) {
            $payload['flightClasses'] = [$googleWalletData['flightClass']];
            $payload['flightObjects'] = [$googleWalletData['flightObject']];
        } elseif (isset($googleWalletData['eventTicketClass'], $googleWalletData['eventTicketObject'])) {
            $payload['eventTicketClasses'] = [$googleWalletData['eventTicketClass']];
            $payload['eventTicketObjects'] = [$googleWalletData['eventTicketObject']];
        } elseif (isset($googleWalletData['loyaltyClass'], $googleWalletData['loyaltyObject'])) {
            $payload['loyaltyClasses'] = [$googleWalletData['loyaltyClass']];
            $payload['loyaltyObjects'] = [$googleWalletData['loyaltyObject']];
        } else {
            $payload['genericClasses'] = [$googleWalletData['genericClass'] ?? []];
            $payload['genericObjects'] = [$googleWalletData['genericObject'] ?? []];
        }

        return [
            'iss' => $issuerId . '@developer.gserviceaccount.com',
            'aud' => 'google',
            'typ' => 'savetogooglewallet',
            'origins' => ['https://stackhal.com'],
            'payload' => $payload,
        ];
    }
}
