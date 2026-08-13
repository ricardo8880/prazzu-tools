<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Quality\E2E;

use App\Core\Quality\E2E\Data\ToolDownloadExpectation;
use App\Core\Quality\E2E\Data\ToolScenario;
use PHPUnit\Framework\TestCase;

final class ToolScenarioSerializationTest extends TestCase
{
    public function test_scenario_can_be_restored_from_var_export_for_laravel_config_cache(): void
    {
        $scenario = new ToolScenario(
            id: 'fluxo-principal-valido',
            title: 'Executa o fluxo principal',
            kind: 'valid',
            toolSlug: 'ferramenta-exemplo',
            steps: [['action' => 'submit', 'scope_test_id' => 'tool-form-panel']],
            expectations: [['type' => 'visible', 'test_id' => 'tool-result']],
            tags: ['regression'],
            downloads: [new ToolDownloadExpectation(
                id: 'resultado-pdf',
                testId: 'download-pdf',
                format: 'pdf',
                extension: 'pdf',
                minimumBytes: 800,
                filenameContains: 'resultado',
                mimeType: 'application/pdf',
            )],
        );

        $restored = eval('return '.var_export($scenario, true).';');

        self::assertInstanceOf(ToolScenario::class, $restored);
        self::assertSame($scenario->toArray(), $restored->toArray());
    }
}
