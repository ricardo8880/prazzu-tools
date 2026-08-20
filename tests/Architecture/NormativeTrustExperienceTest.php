<?php

declare(strict_types=1);

namespace Tests\Architecture;

use Tests\TestCase;

final class NormativeTrustExperienceTest extends TestCase
{
    public function test_shared_normative_trust_surface_exposes_verification_vigency_sources_and_scope(): void
    {
        $view = file_get_contents(base_path('resources/views/components/tools/normative-trust.blade.php'));

        self::assertIsString($view);
        self::assertStringContainsString('Confiança do resultado', $view);
        self::assertStringContainsString('Base normativa e fontes oficiais', $view);
        self::assertStringContainsString('Referência do cálculo', $view);
        self::assertStringContainsString('Vigência', $view);
        self::assertStringContainsString('Última verificação registrada', $view);
        self::assertStringContainsString('Fontes utilizadas', $view);
        self::assertStringContainsString('Premissas e limites deste resultado', $view);
        self::assertStringContainsString('rel="noopener noreferrer"', $view);
    }

    public function test_tools_that_expose_normative_rules_also_expose_the_shared_trust_surface(): void
    {
        $modules = [
            'DifalIcmsCalculator',
            'EmployeeCostCalculator',
            'EmployerInssCalculator',
            'FactorRSimulator',
            'IcmsStCalculator',
            'InvoiceWithholdingCalculator',
            'LaborChargesCalculator',
            'LateDasCalculator',
            'NetSalaryCalculator',
            'OvertimeCalculator',
            'PisCofinsCalculator',
            'PresumedProfitIrpjCsllCalculator',
            'ProLaboreProfitDistributionCalculator',
            'ProLaboreSimulator',
            'RetroactiveDasRegularizationCalculator',
            'TaxRegimeComparator',
        ];

        foreach ($modules as $module) {
            $view = file_get_contents(base_path("app/Tools/{$module}/Resources/views/index.blade.php"));

            self::assertIsString($view);
            self::assertStringContainsString(
                '<x-tools.normative-trust',
                $view,
                $module.' possui regras normativas no resultado e precisa expor a superfície compartilhada de confiança.',
            );
        }
    }

    public function test_net_salary_no_longer_renders_the_old_technical_only_rule_list(): void
    {
        $view = file_get_contents(base_path('app/Tools/NetSalaryCalculator/Resources/views/index.blade.php'));

        self::assertIsString($view);
        self::assertStringContainsString('<x-tools.normative-trust', $view);
        self::assertStringNotContainsString('<h3 class="h5">Regras normativas utilizadas</h3>', $view);
    }
    public function test_every_module_that_creates_normative_snapshots_exposes_the_shared_trust_surface(): void
    {
        $modules = [];

        foreach (glob(base_path('app/Tools/*'), GLOB_ONLYDIR) ?: [] as $moduleRoot) {
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($moduleRoot));
            $createsSnapshot = false;

            foreach ($iterator as $file) {
                if (! $file->isFile() || $file->getExtension() !== 'php' || str_contains($file->getPathname(), DIRECTORY_SEPARATOR.'Tests'.DIRECTORY_SEPARATOR)) {
                    continue;
                }

                $contents = file_get_contents($file->getPathname());
                if (is_string($contents) && str_contains($contents, 'NormativeRuleSnapshot::fromRule')) {
                    $createsSnapshot = true;
                    break;
                }
            }

            if ($createsSnapshot) {
                $modules[] = basename($moduleRoot);
            }
        }

        self::assertNotEmpty($modules);

        foreach ($modules as $module) {
            $viewPath = base_path("app/Tools/{$module}/Resources/views/index.blade.php");
            self::assertFileExists($viewPath, $module.' gera snapshot normativo e precisa possuir uma view de resultado auditável.');

            $view = file_get_contents($viewPath);
            self::assertIsString($view);
            self::assertStringContainsString(
                '<x-tools.normative-trust',
                $view,
                $module.' gera snapshot normativo e precisa expor a superfície compartilhada de confiança.',
            );
        }
    }

}
