<?php

declare(strict_types=1);

namespace App\Tests\Mcp;

use App\Mcp\PkpassTools;
use App\Pkpass\Application\PkpassInspector;
use App\Pkpass\Domain\Engine\PkpassValidator;
use PHPUnit\Framework\TestCase;

final class PkpassToolsTest extends TestCase
{
    private PkpassTools $tools;

    protected function setUp(): void
    {
        $validator = new PkpassValidator();
        $inspector = new PkpassInspector($validator);
        $this->tools = new PkpassTools($inspector, $validator);
    }

    public function testInspectApplePkpassToolReturnsValidationJson(): void
    {
        $validPass = json_encode([
            'formatVersion' => 1,
            'passTypeIdentifier' => 'pass.com.acme.card',
            'serialNumber' => 'GEN-100',
            'teamIdentifier' => 'ABCDE12345',
            'organizationName' => 'Acme Club',
            'description' => 'Membership Card',
            'generic' => [
                'primaryFields' => [
                    ['key' => 'member', 'label' => 'MEMBER', 'value' => 'Bahdan'],
                ],
            ],
        ], JSON_THROW_ON_ERROR);

        $response = $this->tools->inspectApplePkpass($validPass);
        $data = json_decode($response, true, flags: JSON_THROW_ON_ERROR);

        self::assertSame('completed', $data['status']);
        self::assertTrue($data['result']['is_valid']);
        self::assertSame('generic', $data['result']['pass_type']);
        self::assertSame('Acme Club', $data['result']['organization_name']);
    }

    public function testInspectApplePkpassToolHandlesInvalidJson(): void
    {
        $response = $this->tools->inspectApplePkpass('{ broken json');
        $data = json_decode($response, true, flags: JSON_THROW_ON_ERROR);

        self::assertSame('completed', $data['status']);
        self::assertFalse($data['result']['is_valid']);
        self::assertSame('ERR_INVALID_JSON', $data['result']['findings'][0]['code']);
    }

    public function testGenerateApplePkpassSpecReturnsValidPass(): void
    {
        $res = $this->tools->generateApplePkpassSpec(
            pass_type: 'boardingPass',
            organization_name: 'LOT Polish Airlines',
            description: 'Flight WAW to JFK',
            pass_type_identifier: 'pass.com.lot.flights',
            team_identifier: 'BAHDAN9988',
            serial_number: 'LO-027',
        );

        $data = json_decode($res, true, flags: JSON_THROW_ON_ERROR);
        self::assertSame('completed', $data['status']);
        self::assertSame('boardingPass', $data['pass_type']);

        $pass = json_decode($data['pass_json'], true, flags: JSON_THROW_ON_ERROR);
        self::assertSame(1, $pass['formatVersion']);
        self::assertSame('pass.com.lot.flights', $pass['passTypeIdentifier']);
        self::assertSame('BAHDAN9988', $pass['teamIdentifier']);
        self::assertSame('LO-027', $pass['serialNumber']);
        self::assertArrayHasKey('boardingPass', $pass);
        self::assertSame('PKTransitTypeAir', $pass['boardingPass']['transitType']);
    }

    public function testRepairApplePkpassSpecFixesBrokenManifest(): void
    {
        $broken = json_encode([
            'passTypeIdentifier' => 'no-pass-prefix.com',
            'teamIdentifier' => 'TOO_SHORT',
            'expirationDate' => '2026-10-10 14:00:00', // Missing timezone
            'backgroundColor' => 'rgb(20, 20, 20)',
            'foregroundColor' => 'rgb(25, 25, 25)', // Low contrast
            'boardingPass' => [
                // Missing transitType
                'primaryFields' => [
                    ['key' => 'origin', 'value' => 'SFO'],
                ],
            ],
        ], JSON_THROW_ON_ERROR);

        $res = $this->tools->repairApplePkpassSpec($broken);
        $data = json_decode($res, true, flags: JSON_THROW_ON_ERROR);

        self::assertSame('completed', $data['status']);
        self::assertNotEmpty($data['fixes_applied']);

        $repaired = json_decode($data['repaired_json'], true, flags: JSON_THROW_ON_ERROR);
        self::assertSame(1, $repaired['formatVersion']);
        self::assertStringStartsWith('pass.', $repaired['passTypeIdentifier']);
        self::assertSame(10, strlen($repaired['teamIdentifier']));
        self::assertStringEndsWith('Z', $repaired['expirationDate']);
        self::assertSame('PKTransitTypeAir', $repaired['boardingPass']['transitType']);
    }
}
