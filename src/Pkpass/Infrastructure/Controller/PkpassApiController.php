<?php

declare(strict_types=1);

namespace App\Pkpass\Infrastructure\Controller;

use App\Pkpass\Application\PkpassInspector;
use App\Pkpass\Application\PkpassToGoogleWalletConverter;
use App\Pkpass\Domain\Engine\PkpassValidator;
use App\Pkpass\Domain\Model\ValidationFinding;
use App\Pkpass\Domain\Model\ValidationSeverity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/v1/pkpass', name: 'api_pkpass_')]
final class PkpassApiController extends AbstractController
{
    public function __construct(
        private readonly PkpassValidator $validator,
        private readonly PkpassInspector $inspector,
        private readonly PkpassToGoogleWalletConverter $googleWalletConverter,
    ) {
    }

    #[Route(path: '/validate', name: 'validate', methods: ['POST'])]
    public function validate(Request $request): JsonResponse
    {
        /** @var UploadedFile|null $file */
        $file = $request->files->get('file') ?? $request->files->get('pkpass');

        if ($file instanceof UploadedFile) {
            return $this->validatePkpassArchive($file);
        }

        $rawBody = (string) $request->getContent();
        if (trim($rawBody) === '') {
            return new JsonResponse([
                'valid' => false,
                'error' => 'Empty request body. Send pass.json or multipart/form-data with .pkpass file.',
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            /** @var array<string, mixed> $passData */
            $passData = json_decode($rawBody, true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return new JsonResponse([
                'valid' => false,
                'score' => 0,
                'counts' => ['errors' => 1, 'warnings' => 0, 'info' => 0],
                'findings' => [
                    [
                        'code' => 'ERR_INVALID_JSON',
                        'severity' => 'error',
                        'title' => 'Invalid JSON Syntax',
                        'message' => $e->getMessage(),
                        'file' => 'pass.json',
                        'path' => null,
                        'remediation' => 'Ensure valid JSON syntax.',
                    ],
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $result = $this->validator->validate($passData);
        $score = $this->calculateScore($result->findings);
        $errorsCount = $result->errorCount();
        $warningsCount = $result->warningCount();

        $responsePayload = [
            'valid' => $result->isValid,
            'passType' => $result->passType?->value,
            'serialNumber' => $result->serialNumber,
            'score' => $score,
            'counts' => [
                'errors' => $errorsCount,
                'warnings' => $warningsCount,
                'info' => 0,
            ],
            'findings' => array_map(
                fn (ValidationFinding $f): array => [
                    'code' => $f->code,
                    'severity' => $f->severity->value,
                    'title' => $f->title,
                    'message' => $f->description,
                    'file' => $f->file ?? 'pass.json',
                    'path' => $f->field,
                    'remediation' => $f->remediation ?? $this->suggestRemediation($f->code),
                ],
                $result->findings
            ),
            'manifest' => null,
            'signature' => null,
        ];

        $statusCode = $result->isValid ? Response::HTTP_OK : Response::HTTP_UNPROCESSABLE_ENTITY;

        return new JsonResponse($responsePayload, $statusCode);
    }

    #[Route(path: '/convert/google-wallet', name: 'convert_google_wallet', methods: ['POST'])]
    public function convertGoogleWallet(Request $request): JsonResponse
    {
        /** @var UploadedFile|null $file */
        $file = $request->files->get('file') ?? $request->files->get('pkpass');

        if ($file instanceof UploadedFile) {
            $passData = $this->extractPassJsonFromArchive($file);
            if ($passData === null) {
                return new JsonResponse([
                    'status' => 'error',
                    'error' => 'Could not extract valid pass.json from uploaded .pkpass archive.',
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        } else {
            $rawBody = (string) $request->getContent();
            try {
                /** @var array<string, mixed> $passData */
                $passData = json_decode($rawBody, true, flags: JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                return new JsonResponse([
                    'status' => 'error',
                    'error' => 'Invalid JSON payload: ' . $e->getMessage(),
                ], Response::HTTP_BAD_REQUEST);
            }
        }

        $conversion = $this->googleWalletConverter->convert($passData);

        return new JsonResponse($conversion, Response::HTTP_OK);
    }

    private function validatePkpassArchive(UploadedFile $file): JsonResponse
    {
        $zip = new \ZipArchive();
        $openResult = $zip->open($file->getPathname());
        if ($openResult !== true) {
            return new JsonResponse([
                'valid' => false,
                'score' => 0,
                'counts' => ['errors' => 1, 'warnings' => 0, 'info' => 0],
                'findings' => [
                    [
                        'code' => 'ERR_INVALID_ARCHIVE',
                        'severity' => 'error',
                        'title' => 'Invalid ZIP Archive',
                        'message' => 'The uploaded file could not be parsed as a valid ZIP/.pkpass bundle.',
                        'file' => $file->getClientOriginalName(),
                        'path' => null,
                        'remediation' => 'Verify that the file was created using valid zip compression.',
                    ],
                ],
                'manifest' => null,
                'signature' => null,
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $files = [];
        $hashes = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if ($name === false || str_ends_with($name, '/')) {
                continue;
            }
            $content = $zip->getFromIndex($i);
            if ($content === false) {
                continue;
            }
            $files[$name] = $content;
            $hashes[$name] = sha1($content);
        }
        $zip->close();

        $passJsonContent = $files['pass.json'] ?? null;
        if ($passJsonContent === null) {
            return new JsonResponse([
                'valid' => false,
                'score' => 0,
                'counts' => ['errors' => 1, 'warnings' => 0, 'info' => 0],
                'findings' => [
                    [
                        'code' => 'ERR_MISSING_PASS_JSON',
                        'severity' => 'error',
                        'title' => 'Missing pass.json',
                        'message' => 'The .pkpass archive must contain a root-level pass.json manifest.',
                        'file' => 'pass.json',
                        'path' => null,
                        'remediation' => 'Include a valid pass.json file at the root of the .pkpass archive.',
                    ],
                ],
                'manifest' => null,
                'signature' => null,
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            /** @var array<string, mixed> $passData */
            $passData = json_decode($passJsonContent, true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return new JsonResponse([
                'valid' => false,
                'score' => 0,
                'counts' => ['errors' => 1, 'warnings' => 0, 'info' => 0],
                'findings' => [
                    [
                        'code' => 'ERR_INVALID_JSON',
                        'severity' => 'error',
                        'title' => 'Invalid JSON in pass.json',
                        'message' => $e->getMessage(),
                        'file' => 'pass.json',
                        'path' => null,
                        'remediation' => 'Ensure valid JSON syntax inside pass.json.',
                    ],
                ],
                'manifest' => null,
                'signature' => null,
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $validationResult = $this->validator->validate($passData);
        $allFindings = $validationResult->findings;

        // Manifest verification
        $manifestVerified = false;
        $missingFiles = [];
        $unmanifestedFiles = [];
        if (isset($files['manifest.json'])) {
            try {
                /** @var array<string, string> $manifestMap */
                $manifestMap = json_decode($files['manifest.json'], true, flags: JSON_THROW_ON_ERROR);
                $manifestFindings = $this->inspector->verifyManifest($manifestMap, $hashes);
                $allFindings = array_merge($allFindings, $manifestFindings);
                $manifestVerified = count($manifestFindings) === 0;

                foreach ($manifestFindings as $mf) {
                    if ($mf->code === 'ERR_MISSING_ARCHIVE_FILE' && $mf->file !== null) {
                        $missingFiles[] = $mf->file;
                    }
                    if ($mf->code === 'ERR_MISSING_MANIFEST_ENTRY' && $mf->file !== null) {
                        $unmanifestedFiles[] = $mf->file;
                    }
                }
            } catch (\JsonException) {
                $allFindings[] = new ValidationFinding(
                    'ERR_INVALID_MANIFEST_JSON',
                    ValidationSeverity::Error,
                    'Invalid manifest.json',
                    'manifest.json contains invalid JSON syntax.',
                    null,
                    'manifest.json'
                );
            }
        } else {
            $allFindings[] = new ValidationFinding(
                'ERR_MISSING_MANIFEST',
                ValidationSeverity::Error,
                'Missing manifest.json',
                'The archive does not contain a manifest.json file.',
                null,
                'manifest.json'
            );
        }

        // Signature check
        $signatureInfo = $this->inspectSignature($files['signature'] ?? null);
        if ($signatureInfo['present'] === false) {
            $allFindings[] = new ValidationFinding(
                'INFO_UNSIGNED_PASS',
                ValidationSeverity::Info,
                'Unsigned Pass Package',
                'Pass archive does not contain a PKCS#7 signature. It cannot be installed on iOS until signed.',
                null,
                'signature'
            );
        }

        // Required icon check
        if (!isset($files['icon.png']) && !isset($files['icon@2x.png'])) {
            $allFindings[] = new ValidationFinding(
                'ERR_MISSING_REQUIRED_ICON',
                ValidationSeverity::Error,
                'Missing Icon Asset',
                'Apple Wallet passes require an icon.png or icon@2x.png asset for lock screen notifications.',
                null,
                'icon.png'
            );
        }

        $errorCount = count(array_filter($allFindings, static fn ($f) => $f->severity === ValidationSeverity::Error));
        $warningCount = count(array_filter($allFindings, static fn ($f) => $f->severity === ValidationSeverity::Warning));
        $infoCount = count(array_filter($allFindings, static fn ($f) => $f->severity === ValidationSeverity::Info));

        $isValid = $errorCount === 0;
        $score = $this->calculateScore($allFindings);

        $responsePayload = [
            'valid' => $isValid,
            'passType' => $validationResult->passType?->value,
            'serialNumber' => $validationResult->serialNumber,
            'score' => $score,
            'counts' => [
                'errors' => $errorCount,
                'warnings' => $warningCount,
                'info' => $infoCount,
            ],
            'findings' => array_map(
                fn (ValidationFinding $f): array => [
                    'code' => $f->code,
                    'severity' => $f->severity->value,
                    'title' => $f->title,
                    'message' => $f->description,
                    'file' => $f->file ?? 'pass.json',
                    'path' => $f->field,
                    'remediation' => $f->remediation ?? $this->suggestRemediation($f->code),
                ],
                $allFindings
            ),
            'manifest' => [
                'verified' => $manifestVerified,
                'totalFiles' => count($files),
                'missingFiles' => $missingFiles,
                'unmanifestedFiles' => $unmanifestedFiles,
            ],
            'signature' => $signatureInfo,
        ];

        $statusCode = $isValid ? Response::HTTP_OK : Response::HTTP_UNPROCESSABLE_ENTITY;

        return new JsonResponse($responsePayload, $statusCode);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function extractPassJsonFromArchive(UploadedFile $file): ?array
    {
        $zip = new \ZipArchive();
        if ($zip->open($file->getPathname()) !== true) {
            return null;
        }

        $content = $zip->getFromName('pass.json');
        $zip->close();

        if ($content === false) {
            return null;
        }

        try {
            /** @var array<string, mixed> $data */
            $data = json_decode($content, true, flags: JSON_THROW_ON_ERROR);
            return $data;
        } catch (\JsonException) {
            return null;
        }
    }

    /**
     * @return array{
     *   present: bool,
     *   algorithm: string|null,
     *   teamIdentifier: string|null,
     *   passTypeIdentifier: string|null,
     *   expiresAt: string|null,
     *   isExpired: bool|null
     * }
     */
    private function inspectSignature(?string $signatureBytes): array
    {
        if ($signatureBytes === null || $signatureBytes === '') {
            return [
                'present' => false,
                'algorithm' => null,
                'teamIdentifier' => null,
                'passTypeIdentifier' => null,
                'expiresAt' => null,
                'isExpired' => null,
            ];
        }

        $teamIdentifier = null;
        $passTypeIdentifier = null;
        $expiresAt = null;
        $isExpired = false;
        $algorithm = 'sha256WithRSAEncryption';

        if (preg_match('/([A-Z0-9]{10})/', $signatureBytes, $m)) {
            $teamIdentifier = $m[1];
        }
        if (preg_match('/(pass\.[a-zA-Z0-9.\-_]+)/', $signatureBytes, $m)) {
            $passTypeIdentifier = $m[1];
        }

        if (preg_match_all('/\d{12,14}Z/', $signatureBytes, $dateMatches) && count($dateMatches[0]) >= 2) {
            $dateStr = $dateMatches[0][1];
            try {
                $dt = new \DateTimeImmutable($dateStr);
                $expiresAt = $dt->format(\DateTimeInterface::ATOM);
                $isExpired = $dt < new \DateTimeImmutable();
            } catch (\Exception) {
                // Ignore date parsing failure
            }
        }

        return [
            'present' => true,
            'algorithm' => $algorithm,
            'teamIdentifier' => $teamIdentifier,
            'passTypeIdentifier' => $passTypeIdentifier,
            'expiresAt' => $expiresAt,
            'isExpired' => $isExpired,
        ];
    }

    /**
     * @param list<ValidationFinding> $findings
     */
    private function calculateScore(array $findings): int
    {
        $score = 100;
        foreach ($findings as $f) {
            if ($f->severity === ValidationSeverity::Error) {
                $score -= 25;
            } elseif ($f->severity === ValidationSeverity::Warning) {
                $score -= 8;
            }
        }
        return max(0, min(100, $score));
    }

    private function suggestRemediation(string $code): string
    {
        return match ($code) {
            'ERR_INVALID_DATE_TIMEZONE' => "Append 'Z' or provide offset like '+02:00'.",
            'WARN_LOW_COLOR_CONTRAST' => 'Use a higher contrast foreground or label color for legibility.',
            'ERR_MISSING_REQUIRED_KEY' => 'Declare all mandatory Apple Wallet pass metadata keys.',
            'ERR_INVALID_PASS_STYLE' => 'Include exactly one pass style dictionary (boardingPass, eventTicket, coupon, storeCard, generic).',
            'ERR_INVALID_TRANSIT_TYPE' => 'Specify transitType: PKTransitTypeAir, PKTransitTypeTrain, etc.',
            'ERR_MANIFEST_MISMATCH' => 'Recalculate SHA-1 hash for the file in manifest.json.',
            'ERR_MISSING_ARCHIVE_FILE' => 'Add the missing declared file into the .pkpass ZIP bundle.',
            'ERR_MISSING_MANIFEST_ENTRY' => 'Add the file and its SHA-1 hash into manifest.json.',
            default => 'Review Apple PassKit guidelines and correct the reported schema property.',
        };
    }
}
