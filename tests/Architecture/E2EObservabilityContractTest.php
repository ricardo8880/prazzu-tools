<?php

declare(strict_types=1);

namespace Tests\Architecture;

use App\Core\Quality\E2E\Support\E2ECorrelation;
use Tests\TestCase;

final class E2EObservabilityContractTest extends TestCase
{
    public function test_observability_contract_is_registered_and_safe(): void
    {
        $config = require base_path('config/e2e_observability.php');

        self::assertSame('1.0.0', $config['schema_version']);
        self::assertSame('X-E2E-Run-Id', E2ECorrelation::RUN_HEADER);
        self::assertSame('X-E2E-Scenario-Id', E2ECorrelation::SCENARIO_HEADER);
        self::assertContains('password', $config['sensitive_fields']);
        self::assertFileExists(base_path('scripts/e2e-observability.php'));
        self::assertStringContainsString('CorrelateE2ERequest::class', file_get_contents(base_path('bootstrap/app.php')));
        self::assertStringContainsString("'e2e' => [", file_get_contents(base_path('config/logging.php')));
    }

    public function test_correlation_identifiers_are_normalized(): void
    {
        self::assertSame('tool:valid-case', E2ECorrelation::normalize(' Tool:Valid Case ', 'scenario'));
        self::assertLessThanOrEqual(120, strlen(E2ECorrelation::normalize(str_repeat('x', 200), 'scenario')));
    }
}
