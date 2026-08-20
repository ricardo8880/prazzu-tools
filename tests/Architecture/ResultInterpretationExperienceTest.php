<?php

declare(strict_types=1);

namespace Tests\Architecture;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class ResultInterpretationExperienceTest extends TestCase
{
    /** @return array<string, array{string}> */
    public static function interpretedToolViews(): array
    {
        return [
            'salario liquido' => ['app/Tools/NetSalaryCalculator/Resources/views/index.blade.php'],
            'hora extra' => ['app/Tools/OvertimeCalculator/Resources/views/index.blade.php'],
            'icms st' => ['app/Tools/IcmsStCalculator/Resources/views/index.blade.php'],
            'pis cofins' => ['app/Tools/PisCofinsCalculator/Resources/views/index.blade.php'],
            'lucro presumido' => ['app/Tools/PresumedProfitIrpjCsllCalculator/Resources/views/index.blade.php'],
            'pro labore' => ['app/Tools/ProLaboreSimulator/Resources/views/index.blade.php'],
            'custo funcionario' => ['app/Tools/EmployeeCostCalculator/Resources/views/index.blade.php'],
        ];
    }

    public function test_shared_result_insight_is_presentational_only(): void
    {
        $view = file_get_contents(resource_path('views/components/tools/result-insight.blade.php'));

        self::assertIsString($view);
        self::assertStringContainsString('data-result-insight', $view);
        self::assertStringContainsString('Leitura rápida', $view);
        self::assertStringNotContainsString('App\\Tools', $view);
        self::assertStringNotContainsString('NormativeRule', $view);
        self::assertStringNotContainsString('PlusFeature', $view);
    }

    #[DataProvider('interpretedToolViews')]
    public function test_selected_dense_tools_expose_domain_specific_result_reading(string $viewPath): void
    {
        $view = file_get_contents(base_path($viewPath));

        self::assertIsString($view);
        self::assertStringContainsString('<x-tools.result-insight', $view);
        self::assertStringContainsString('data-analytics-result="main"', $view);
        self::assertStringContainsString('result-panel', $view);
    }
}
