<?php

declare(strict_types=1);

namespace App\Tests\Pkpass;

use App\Pkpass\Application\PkpassInspector;
use App\Pkpass\Application\PkpassToGoogleWalletConverter;
use App\Pkpass\Domain\Engine\PkpassValidator;
use App\Pkpass\Infrastructure\Controller\PkpassApiController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class PkpassApiControllerTest extends TestCase
{
    private PkpassApiController $controller;

    protected function setUp(): void
    {
        $validator = new PkpassValidator();
        $inspector = new PkpassInspector($validator);
        $converter = new PkpassToGoogleWalletConverter();

        $this->controller = new PkpassApiController($validator, $inspector, $converter);
    }

    public function testValidateWithValidJsonReturns200(): void
    {
        $payload = json_encode([
            'formatVersion' => 1,
            'passTypeIdentifier' => 'pass.com.stackhal.travel',
            'serialNumber' => 'LOT-89421',
            'teamIdentifier' => 'BAHDAN9988',
            'organizationName' => 'Stackhal Airlines',
            'description' => 'Warsaw to New York Flight',
            'boardingPass' => [
                'transitType' => 'PKTransitTypeAir',
                'primaryFields' => [
                    ['key' => 'origin', 'label' => 'WARSAW', 'value' => 'WAW'],
                    ['key' => 'dest', 'label' => 'NEW YORK', 'value' => 'JFK'],
                ],
            ],
            'barcodes' => [
                [
                    'format' => 'PKBarcodeFormatPDF417',
                    'message' => 'M1HAL/BAHDAN ELO027 123Y004A0012 100',
                ],
            ],
        ], JSON_THROW_ON_ERROR);

        $request = Request::create('/api/v1/pkpass/validate', 'POST', [], [], [], ['CONTENT_TYPE' => 'application/json'], $payload);
        $response = $this->controller->validate($request);

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        /** @var array<string, mixed> $data */
        $data = json_decode((string) $response->getContent(), true, flags: JSON_THROW_ON_ERROR);

        self::assertTrue($data['valid']);
        self::assertSame('boardingPass', $data['passType']);
        self::assertSame('LOT-89421', $data['serialNumber']);
        self::assertSame(100, $data['score']);
        self::assertSame(0, $data['counts']['errors']);
    }

    public function testValidateWithInvalidJsonReturns422(): void
    {
        $payload = json_encode([
            'formatVersion' => 1,
            // Missing passTypeIdentifier, teamIdentifier, organizationName, description
            'serialNumber' => 'MISSING-FIELDS',
            'expirationDate' => '2026-10-10 12:00:00', // Missing timezone
        ], JSON_THROW_ON_ERROR);

        $request = Request::create('/api/v1/pkpass/validate', 'POST', [], [], [], ['CONTENT_TYPE' => 'application/json'], $payload);
        $response = $this->controller->validate($request);

        self::assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        /** @var array<string, mixed> $data */
        $data = json_decode((string) $response->getContent(), true, flags: JSON_THROW_ON_ERROR);

        self::assertFalse($data['valid']);
        self::assertGreaterThanOrEqual(1, $data['counts']['errors']);
        self::assertLessThan(100, $data['score']);
    }

    public function testValidateWithSyntaxErrorReturns422(): void
    {
        $request = Request::create('/api/v1/pkpass/validate', 'POST', [], [], [], ['CONTENT_TYPE' => 'application/json'], '{ bad json:');
        $response = $this->controller->validate($request);

        self::assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        /** @var array<string, mixed> $data */
        $data = json_decode((string) $response->getContent(), true, flags: JSON_THROW_ON_ERROR);

        self::assertFalse($data['valid']);
        self::assertSame('ERR_INVALID_JSON', $data['findings'][0]['code']);
    }

    public function testValidateWithEmptyBodyReturns400(): void
    {
        $request = Request::create('/api/v1/pkpass/validate', 'POST', [], [], [], ['CONTENT_TYPE' => 'application/json'], '');
        $response = $this->controller->validate($request);

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testValidateWithPkpassArchive(): void
    {
        $tempZip = tempnam(sys_get_temp_dir(), 'pkpass_test_') . '.zip';
        $zip = new \ZipArchive();
        $zip->open($tempZip, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $passData = [
            'formatVersion' => 1,
            'passTypeIdentifier' => 'pass.com.stackhal.event',
            'serialNumber' => 'CONCERT-99',
            'teamIdentifier' => 'BAHDAN9988',
            'organizationName' => 'Stackhal Music',
            'description' => 'Symphony Concert',
            'eventTicket' => [
                'primaryFields' => [
                    ['key' => 'concert', 'label' => 'ORCHESTRA', 'value' => 'Philharmonic'],
                ],
            ],
        ];
        $passJson = (string) json_encode($passData, JSON_THROW_ON_ERROR);
        $iconData = 'mock-icon-bytes';

        $manifest = [
            'pass.json' => sha1($passJson),
            'icon.png' => sha1($iconData),
        ];
        $manifestJson = (string) json_encode($manifest, JSON_THROW_ON_ERROR);

        $zip->addFromString('pass.json', $passJson);
        $zip->addFromString('manifest.json', $manifestJson);
        $zip->addFromString('icon.png', $iconData);
        $zip->close();

        $uploadedFile = new UploadedFile($tempZip, 'ticket.pkpass', 'application/vnd.apple.pkpass', null, true);

        $request = Request::create('/api/v1/pkpass/validate', 'POST', [], [], ['file' => $uploadedFile]);
        $response = $this->controller->validate($request);

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        /** @var array<string, mixed> $data */
        $data = json_decode((string) $response->getContent(), true, flags: JSON_THROW_ON_ERROR);

        self::assertTrue($data['valid']);
        self::assertTrue($data['manifest']['verified']);
        self::assertSame(3, $data['manifest']['totalFiles']);
        self::assertEmpty($data['manifest']['missingFiles']);

        @unlink($tempZip);
    }

    public function testConvertGoogleWalletWithBoardingPass(): void
    {
        $payload = json_encode([
            'passTypeIdentifier' => 'pass.com.stackhal.travel',
            'serialNumber' => 'FLIGHT-101',
            'organizationName' => 'Stackhal Air',
            'backgroundColor' => 'rgb(15, 23, 42)',
            'boardingPass' => [
                'transitType' => 'PKTransitTypeAir',
                'primaryFields' => [
                    ['key' => 'origin', 'value' => 'SFO'],
                    ['key' => 'dest', 'value' => 'WAW'],
                ],
                'secondaryFields' => [
                    ['key' => 'passenger', 'value' => 'Bahdan Hal'],
                ],
            ],
        ], JSON_THROW_ON_ERROR);

        $request = Request::create('/api/v1/pkpass/convert/google-wallet', 'POST', [], [], [], ['CONTENT_TYPE' => 'application/json'], $payload);
        $response = $this->controller->convertGoogleWallet($request);

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        /** @var array<string, mixed> $data */
        $data = json_decode((string) $response->getContent(), true, flags: JSON_THROW_ON_ERROR);

        self::assertSame('success', $data['status']);
        self::assertSame('boardingPass', $data['passType']);
        self::assertArrayHasKey('flightClass', $data['googleWallet']);
        self::assertArrayHasKey('flightObject', $data['googleWallet']);
        self::assertSame('Bahdan Hal', $data['googleWallet']['flightObject']['passengerName']);
        self::assertStringStartsWith('https://pay.google.com/gp/v/save/', $data['saveUrl']);
    }
}
