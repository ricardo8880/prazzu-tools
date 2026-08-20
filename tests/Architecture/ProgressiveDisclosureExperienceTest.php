<?php

declare(strict_types=1);

namespace Tests\Architecture;

use Tests\TestCase;

final class ProgressiveDisclosureExperienceTest extends TestCase
{
    public function test_shared_disclosure_is_native_accessible_and_does_not_require_javascript(): void
    {
        $view = file_get_contents(base_path('resources/views/components/tools/form-disclosure.blade.php'));

        self::assertIsString($view);
        self::assertStringContainsString('<details', $view);
        self::assertStringContainsString('<summary', $view);
        self::assertStringContainsString('Mostrar campos', $view);
        self::assertStringContainsString('@if($open) open @endif', $view);
        self::assertStringNotContainsString('data-bs-toggle', $view);
    }

    public function test_high_density_forms_use_shared_progressive_disclosure_for_optional_or_advanced_inputs(): void
    {
        $modules = [
            'EmployeeCostCalculator',
            'IcmsStCalculator',
            'NetSalaryCalculator',
            'OvertimeCalculator',
            'PisCofinsCalculator',
            'PresumedProfitIrpjCsllCalculator',
            'ProLaboreSimulator',
        ];

        foreach ($modules as $module) {
            $view = file_get_contents(base_path("app/Tools/{$module}/Resources/views/index.blade.php"));

            self::assertIsString($view);
            self::assertStringContainsString(
                '<x-tools.form-disclosure',
                $view,
                $module.' deve manter campos opcionais/avançados fora do caminho principal sem retirar capacidade.',
            );
        }
    }

    public function test_net_salary_keeps_essential_fields_outside_the_optional_disclosure(): void
    {
        $view = file_get_contents(base_path('app/Tools/NetSalaryCalculator/Resources/views/index.blade.php'));

        self::assertIsString($view);
        $disclosurePosition = strpos($view, '<x-tools.form-disclosure');

        self::assertNotFalse($disclosurePosition);
        self::assertLessThan($disclosurePosition, strpos($view, 'name="base_salary"'));
        self::assertLessThan($disclosurePosition, strpos($view, 'name="competence"'));
        self::assertGreaterThan($disclosurePosition, strpos($view, 'name="taxable_additional_earnings"'));
    }
}
