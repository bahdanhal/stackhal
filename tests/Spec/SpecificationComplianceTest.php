<?php

declare(strict_types=1);

namespace App\Tests\Spec;

use App\CaddyTranspiler\Application\CaddyTranspiler;
use PHPUnit\Framework\TestCase;

final class SpecificationComplianceTest extends TestCase
{
    public function testSeoAuditRulesSpecStructure(): void
    {
        $specPath = dirname(__DIR__, 2) . '/specs/seo-audit-rules.spec.json';
        self::assertFileExists($specPath);

        $spec = json_decode((string) file_get_contents($specPath), true, flags: JSON_THROW_ON_ERROR);
        self::assertNotEmpty($spec['rules']);
        self::assertCount(6, $spec['editorial_advisories']);

        foreach ($spec['rules'] as $rule) {
            self::assertArrayHasKey('code', $rule);
            self::assertArrayHasKey('severity', $rule);
            self::assertContains($rule['severity'], $spec['severities']);
            self::assertArrayHasKey('title', $rule);
            self::assertArrayHasKey('description', $rule);
        }

        foreach ($spec['editorial_advisories'] as $advisory) {
            self::assertArrayHasKey('code', $advisory);
            self::assertArrayHasKey('title', $advisory);
            self::assertArrayHasKey('description', $advisory);
        }
    }

    public function testGeoReadinessSpecStructure(): void
    {
        $specPath = dirname(__DIR__, 2) . '/specs/geo-readiness.spec.json';
        self::assertFileExists($specPath);

        $spec = json_decode((string) file_get_contents($specPath), true, flags: JSON_THROW_ON_ERROR);
        self::assertContains('GPTBot', $spec['crawler_user_agents']);
        self::assertContains('ClaudeBot', $spec['crawler_user_agents']);
        self::assertNotEmpty($spec['schema_classifications']['content']);
        self::assertNotEmpty($spec['schema_classifications']['entity']);
    }

    public function testMcpToolsSpecStructure(): void
    {
        $specPath = dirname(__DIR__, 2) . '/specs/mcp-tools.spec.json';
        self::assertFileExists($specPath);

        $spec = json_decode((string) file_get_contents($specPath), true, flags: JSON_THROW_ON_ERROR);
        self::assertCount(9, $spec['tools']);

        $names = array_column($spec['tools'], 'name');
        self::assertContains('audit_website_seo', $names);
        self::assertContains('analyze_geo_readiness', $names);
        self::assertContains('inspect_domain_security', $names);
        self::assertContains('transpile_to_caddyfile', $names);
        self::assertContains('inspect_apple_pkpass', $names);
        self::assertContains('calculate_cidr_overlap', $names);
        self::assertContains('transpile_regex_engine', $names);
        self::assertContains('generate_favicon_suite', $names);
        self::assertContains('trace_dns_delegation', $names);
    }

    public function testCidrMatrixSpecStructure(): void
    {
        $specPath = dirname(__DIR__, 2) . '/specs/cidr-matrix.spec.json';
        self::assertFileExists($specPath);

        $spec = json_decode((string) file_get_contents($specPath), true, flags: JSON_THROW_ON_ERROR);
        self::assertNotEmpty($spec['supported_ip_versions']);
        self::assertNotEmpty($spec['diagnostic_codes']);
        self::assertNotEmpty($spec['presets']);
        self::assertNotEmpty($spec['test_vectors']);
    }

    public function testRegexTranspilerSpecStructure(): void
    {
        $specPath = dirname(__DIR__, 2) . '/specs/regex-transpiler.spec.json';
        self::assertFileExists($specPath);

        $spec = json_decode((string) file_get_contents($specPath), true, flags: JSON_THROW_ON_ERROR);
        self::assertNotEmpty($spec['supported_engines']);
        self::assertNotEmpty($spec['engine_metadata']);
        self::assertNotEmpty($spec['diagnostic_codes']);
        self::assertNotEmpty($spec['presets']);
        self::assertNotEmpty($spec['test_vectors']);
    }

    public function testFaviconSuiteSpecStructure(): void
    {
        $specPath = dirname(__DIR__, 2) . '/specs/favicon-suite.spec.json';
        self::assertFileExists($specPath);

        $spec = json_decode((string) file_get_contents($specPath), true, flags: JSON_THROW_ON_ERROR);
        self::assertNotEmpty($spec['supported_input_formats']);
        self::assertNotEmpty($spec['output_bundle_files']);
        self::assertNotEmpty($spec['recommended_html_tags']);
        self::assertNotEmpty($spec['diagnostic_codes']);
        self::assertNotEmpty($spec['presets']);
        self::assertNotEmpty($spec['test_vectors']);
    }

    public function testFaviconSuiteSpecCompliance(): void
    {
        $specPath = dirname(__DIR__, 2) . '/specs/favicon-suite.spec.json';
        self::assertFileExists($specPath);

        $spec = json_decode((string) file_get_contents($specPath), true, flags: JSON_THROW_ON_ERROR);
        $generator = new \App\FaviconSuite\Domain\Engine\FaviconGenerator();

        foreach ($spec['test_vectors'] as $vector) {
            if (isset($vector['svg_input'])) {
                $result = $generator->generateFromSvg($vector['svg_input']);
                self::assertSame(
                    $vector['expected_valid'],
                    $result->isValid,
                    "Valid match failed for vector: {$vector['description']}"
                );

                if (isset($vector['expected_dark_mode_injected'])) {
                    self::assertSame(
                        $vector['expected_dark_mode_injected'],
                        $result->darkModeInjected,
                        "Dark mode injection mismatch for: {$vector['description']}"
                    );
                }

                if (isset($vector['expected_contains_media_query'])) {
                    self::assertStringContainsString(
                        'prefers-color-scheme',
                        $result->svgContent ?? '',
                        "Missing prefers-color-scheme in: {$vector['description']}"
                    );
                }

                if (isset($vector['expected_manifest_name'])) {
                    self::assertStringContainsString(
                        $vector['expected_manifest_name'],
                        $result->htmlTags[3] ?? '',
                        "Manifest tag mismatch in: {$vector['description']}"
                    );
                }

                if (isset($vector['expected_error_codes'])) {
                    foreach ($vector['expected_error_codes'] as $expectedCode) {
                        self::assertContains(
                            $expectedCode,
                            $result->getErrorCodes(),
                            "Missing error code {$expectedCode} in: {$vector['description']}"
                        );
                    }
                }
            } elseif (isset($vector['raster_metadata'])) {
                $result = $generator->generateFromRasterMetadata($vector['raster_metadata']);
                self::assertSame(
                    $vector['expected_valid'],
                    $result->isValid,
                    "Raster valid match failed for vector: {$vector['description']}"
                );

                if (isset($vector['expected_warnings'])) {
                    foreach ($vector['expected_warnings'] as $expectedWarning) {
                        self::assertContains(
                            $expectedWarning,
                            $result->getWarningCodes(),
                            "Missing warning code {$expectedWarning} in: {$vector['description']}"
                        );
                    }
                }
            }
        }
    }

    public function testDnsDagTracerSpecStructure(): void
    {
        $specPath = dirname(__DIR__, 2) . '/specs/dns-dag-tracer.spec.json';
        self::assertFileExists($specPath);

        $spec = json_decode((string) file_get_contents($specPath), true, flags: JSON_THROW_ON_ERROR);
        self::assertNotEmpty($spec['supported_query_types']);
        self::assertNotEmpty($spec['delegation_hierarchy']);
        self::assertNotEmpty($spec['diagnostic_codes']);
        self::assertNotEmpty($spec['presets']);
        self::assertNotEmpty($spec['test_vectors']);
    }

    public function testDnsDagTracerSpecCompliance(): void
    {
        $specPath = dirname(__DIR__, 2) . '/specs/dns-dag-tracer.spec.json';
        self::assertFileExists($specPath);

        $spec = json_decode((string) file_get_contents($specPath), true, flags: JSON_THROW_ON_ERROR);
        $engine = new \App\DnsDagTracer\Domain\Engine\DnsDagEngine();

        foreach ($spec['test_vectors'] as $vector) {
            $queryType = \App\DnsDagTracer\Domain\Model\QueryType::fromString($vector['query_type'] ?? 'A');
            $result = $engine->trace($vector['domain'], $queryType);

            self::assertSame(
                $vector['expected_status'],
                $result->status,
                "Status mismatch for vector: {$vector['description']}"
            );

            if (isset($vector['expected_dnssec_status'])) {
                self::assertSame(
                    $vector['expected_dnssec_status'],
                    $result->dnssecStatus->value,
                    "DNSSEC status mismatch for: {$vector['description']}"
                );
            }

            if (isset($vector['expected_layer_count'])) {
                self::assertSame(
                    $vector['expected_layer_count'],
                    $result->layerCount,
                    "Layer count mismatch for: {$vector['description']}"
                );
            }

            if (isset($vector['expected_divergence'])) {
                self::assertSame(
                    $vector['expected_divergence'],
                    $result->hasDivergence,
                    "Divergence mismatch for: {$vector['description']}"
                );
            }

            if (isset($vector['expected_error_codes'])) {
                foreach ($vector['expected_error_codes'] as $expectedCode) {
                    self::assertContains(
                        $expectedCode,
                        $result->getErrorCodes(),
                        "Missing error code {$expectedCode} in: {$vector['description']}"
                    );
                }
            }

            if (isset($vector['expected_warning_codes'])) {
                foreach ($vector['expected_warning_codes'] as $expectedWarning) {
                    self::assertContains(
                        $expectedWarning,
                        $result->getWarningCodes(),
                        "Missing warning code {$expectedWarning} in: {$vector['description']}"
                    );
                }
            }
        }
    }


    public function testPkpassInspectorSpecCompliance(): void
    {
        $specPath = dirname(__DIR__, 2) . '/specs/pkpass-inspector.spec.json';
        self::assertFileExists($specPath);

        $spec = json_decode((string) file_get_contents($specPath), true, flags: JSON_THROW_ON_ERROR);
        self::assertNotEmpty($spec['pass_styles']);
        self::assertNotEmpty($spec['transit_types']);
        self::assertNotEmpty($spec['barcode_formats']);
        self::assertNotEmpty($spec['diagnostic_codes']);
        self::assertNotEmpty($spec['test_vectors']);

        $validator = new \App\Pkpass\Domain\Engine\PkpassValidator();

        foreach ($spec['test_vectors'] as $vector) {
            $result = $validator->validate($vector['pass']);
            self::assertSame(
                $vector['expected_valid'],
                $result->isValid,
                "Expected validation match for vector: {$vector['description']}"
            );

            if (isset($vector['expected_pass_type'])) {
                self::assertSame($vector['expected_pass_type'], $result->passType?->value);
            }

            if (isset($vector['expected_error_codes'])) {
                $codes = array_map(static fn ($f) => $f->code, $result->findings);
                foreach ($vector['expected_error_codes'] as $expectedCode) {
                    self::assertContains($expectedCode, $codes, "Missing code {$expectedCode} in {$vector['description']}");
                }
            }
        }
    }

    public function testDomainInspectorSpecStructure(): void
    {
        $specPath = dirname(__DIR__, 2) . '/specs/domain-inspector.spec.json';
        self::assertFileExists($specPath);

        $spec = json_decode((string) file_get_contents($specPath), true, flags: JSON_THROW_ON_ERROR);
        self::assertNotEmpty($spec['protocols']);
        self::assertSame(100, $spec['scoring']['max_score']);

        $protocolNames = array_column($spec['protocols'], 'name');
        self::assertContains('dmarc', $protocolNames);
        self::assertContains('bimi', $protocolNames);
        self::assertContains('mta_sts', $protocolNames);
        self::assertContains('tls_rpt', $protocolNames);
        self::assertContains('spf', $protocolNames);
    }

    public function testCaddyTranspilerSpecCompliance(): void
    {
        $specPath = dirname(__DIR__, 2) . '/specs/caddy-transpiler.spec.json';
        self::assertFileExists($specPath);

        $spec = json_decode((string) file_get_contents($specPath), true, flags: JSON_THROW_ON_ERROR);
        self::assertNotEmpty($spec['supported_source_types']);
        self::assertNotEmpty($spec['presets']);
        self::assertNotEmpty($spec['advisory_rules']);
        self::assertNotEmpty($spec['test_vectors']);

        $transpiler = new CaddyTranspiler();

        foreach ($spec['test_vectors'] as $vector) {
            $result = $transpiler->transpile($vector['input']);
            foreach ($vector['expected_caddyfile_snippets'] as $snippet) {
                self::assertStringContainsString(
                    $snippet,
                    $result->caddyfile,
                    "Expected Caddyfile to contain '{$snippet}' for vector: {$vector['description']}"
                );
            }
        }
    }

    public function testCidrMatrixSpecCompliance(): void
    {
        $specPath = dirname(__DIR__, 2) . '/specs/cidr-matrix.spec.json';
        self::assertFileExists($specPath);

        $spec = json_decode((string) file_get_contents($specPath), true, flags: JSON_THROW_ON_ERROR);
        self::assertNotEmpty($spec['supported_ip_versions']);
        self::assertNotEmpty($spec['diagnostic_codes']);
        self::assertNotEmpty($spec['presets']);
        self::assertNotEmpty($spec['test_vectors']);

        $calculator = new \App\CidrMatrix\Domain\Engine\CidrCalculator();

        foreach ($spec['test_vectors'] as $vector) {
            $parentCidr = $vector['parent_cidr'] ?? null;
            $requestedPrefix = $vector['requested_prefix'] ?? null;

            $result = $calculator->analyze(
                cidrInputs: $vector['cidrs'],
                requestedFreePrefix: $requestedPrefix,
                parentCidrInput: $parentCidr
            );

            if (isset($vector['expected_has_collisions'])) {
                self::assertSame(
                    $vector['expected_has_collisions'],
                    $result->hasCollisions,
                    "Collision match failed for: {$vector['description']}"
                );
            }

            if (isset($vector['expected_collision_count'])) {
                self::assertSame(
                    $vector['expected_collision_count'],
                    $result->collisionCount,
                    "Collision count match failed for: {$vector['description']}"
                );
            }

            if (isset($vector['expected_collisions'])) {
                $actualCollisions = array_map(static fn ($c) => $c->toArray(), $result->collisions);
                self::assertSame(
                    $vector['expected_collisions'],
                    $actualCollisions,
                    "Collisions array mismatch for: {$vector['description']}"
                );
            }

            if (isset($vector['expected_normalized_cidr'])) {
                self::assertNotEmpty($result->parsedBlocks);
                self::assertSame(
                    $vector['expected_normalized_cidr'],
                    $result->parsedBlocks[0]->normalizedCidr,
                    "Normalized CIDR mismatch for: {$vector['description']}"
                );
            }

            if (isset($vector['expected_warnings'])) {
                foreach ($vector['expected_warnings'] as $warningCode) {
                    self::assertContains(
                        $warningCode,
                        $result->warnings,
                        "Missing warning code {$warningCode} for: {$vector['description']}"
                    );
                }
            }

            if (isset($vector['expected_free_cidr'])) {
                self::assertSame(
                    $vector['expected_free_cidr'],
                    $result->freeSubnetCidr,
                    "Free CIDR allocation mismatch for: {$vector['description']}"
                );
            }
        }
    }

    public function testRegexTranspilerSpecCompliance(): void
    {
        $specPath = dirname(__DIR__, 2) . '/specs/regex-transpiler.spec.json';
        self::assertFileExists($specPath);

        $spec = json_decode((string) file_get_contents($specPath), true, flags: JSON_THROW_ON_ERROR);
        self::assertNotEmpty($spec['supported_engines']);
        self::assertNotEmpty($spec['engine_metadata']);
        self::assertNotEmpty($spec['diagnostic_codes']);
        self::assertNotEmpty($spec['presets']);
        self::assertNotEmpty($spec['test_vectors']);

        $service = new \App\RegexTranspiler\Application\RegexTranspilerService();

        foreach ($spec['test_vectors'] as $vector) {
            $source = $vector['source_engine'];
            $target = $vector['target_engine'];
            $pattern = $vector['pattern'];

            $result = $service->transpile($pattern, $source, $target);

            self::assertSame(
                $vector['expected_compatible'],
                $result->isCompatible,
                "Compatibility mismatch for: {$vector['description']}"
            );

            if (isset($vector['expected_pattern'])) {
                self::assertSame(
                    $vector['expected_pattern'],
                    $result->transpiledPattern,
                    "Pattern mismatch for: {$vector['description']}"
                );
            }

            if (isset($vector['expected_warnings'])) {
                $warningCodes = array_map(static fn ($d) => $d->code, $result->diagnostics);
                foreach ($vector['expected_warnings'] as $expectedWarning) {
                    self::assertContains(
                        $expectedWarning,
                        $warningCodes,
                        "Missing expected warning {$expectedWarning} in: {$vector['description']}"
                    );
                }
            }

            if (isset($vector['expected_error_codes'])) {
                $errorCodes = array_map(static fn ($d) => $d->code, $result->diagnostics);
                foreach ($vector['expected_error_codes'] as $expectedError) {
                    self::assertContains(
                        $expectedError,
                        $errorCodes,
                        "Missing expected error {$expectedError} in: {$vector['description']}"
                    );
                }
            }
        }
    }
}
