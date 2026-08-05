<?php

declare(strict_types=1);

namespace Tests\Architecture;

use PHPUnit\Framework\TestCase;

final class E2EQualityInventoryTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $e2e;

    /** @var array<string, mixed> */
    private array $product;

    protected function setUp(): void
    {
        parent::setUp();

        $root = dirname(__DIR__, 2);
        $this->e2e = require $root.'/config/e2e_quality.php';
        $this->product = require $root.'/config/product_tools.php';
    }

    public function test_e2e_inventory_mirrors_the_official_catalog_exactly(): void
    {
        self::assertSame(32, $this->e2e['expected_tool_count']);
        self::assertCount(32, $this->e2e['tools']);

        $official = array_map(
            static fn (array $tool): array => [
                'id' => $tool['id'],
                'module' => $tool['module'],
                'slug' => $tool['slug'],
            ],
            $this->product['official'],
        );
        $e2e = array_map(
            static fn (array $tool): array => [
                'id' => $tool['id'],
                'module' => $tool['module'],
                'slug' => $tool['slug'],
            ],
            $this->e2e['tools'],
        );

        self::assertSame($official, $e2e);
    }

    public function test_every_tool_has_valid_minimum_quality_metadata(): void
    {
        $allowedRisks = ['critical', 'high', 'moderate', 'low'];
        $allowedRiskSources = ['risk_profile', 'e2e_inventory_assessment'];
        $allowedScenarios = ['page_load', 'valid', 'invalid'];
        $allowedSurfaces = [
            'form',
            'result',
            'downloads',
            'history',
            'upload',
            'batch',
            'document_generation',
            'secondary_actions',
        ];
        $allowedFormats = ['pdf', 'xlsx', 'csv', 'json', 'docx', 'zip'];

        foreach ($this->e2e['tools'] as $tool) {
            self::assertContains($tool['risk'], $allowedRisks, $tool['module']);
            self::assertContains($tool['risk_source'], $allowedRiskSources, $tool['module']);
            self::assertSame($allowedScenarios, $tool['required_scenarios'], $tool['module']);
            self::assertContains('form', $tool['surfaces'], $tool['module']);
            self::assertContains('result', $tool['surfaces'], $tool['module']);
            self::assertSame($tool['surfaces'], array_values(array_unique($tool['surfaces'])), $tool['module']);
            self::assertSame($tool['download_formats'], array_values(array_unique($tool['download_formats'])), $tool['module']);

            foreach ($tool['surfaces'] as $surface) {
                self::assertContains($surface, $allowedSurfaces, $tool['module']);
            }

            foreach ($tool['download_formats'] as $format) {
                self::assertContains($format, $allowedFormats, $tool['module']);
            }

            self::assertSame(
                in_array('downloads', $tool['surfaces'], true),
                $tool['download_formats'] !== [],
                $tool['module'],
            );
        }
    }

    public function test_execution_profiles_and_governance_documents_are_complete(): void
    {
        self::assertSame(['smoke', 'regression', 'full', 'exploratory'], array_keys($this->e2e['execution_profiles']));
        self::assertTrue($this->e2e['execution_profiles']['smoke']['blocking']);
        self::assertTrue($this->e2e['execution_profiles']['regression']['blocking']);
        self::assertTrue($this->e2e['execution_profiles']['full']['blocking']);
        self::assertFalse($this->e2e['execution_profiles']['exploratory']['blocking']);

        $root = dirname(__DIR__, 2);
        self::assertFileExists($root.'/'.$this->e2e['source_inventory']);
        self::assertFileExists($root.'/'.$this->e2e['contract_document']);
        self::assertFileExists($root.'/'.$this->e2e['lot_report']);
    }
}
