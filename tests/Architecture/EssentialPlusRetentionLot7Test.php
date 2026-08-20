<?php

declare(strict_types=1);

namespace Tests\Architecture;

use App\Core\Tools\Enums\ToolFeatureTier;
use App\Core\Tools\ToolRegistry;
use Tests\TestCase;

final class EssentialPlusRetentionLot7Test extends TestCase
{
    public function test_transparency_required_to_verify_the_result_is_essential(): void
    {
        $registry = app(ToolRegistry::class);

        $expected = [
            'calculadora-pis-cofins' => ['memory'],
            'calculadora-retencoes-nota-fiscal' => ['memory', 'report'],
        ];

        foreach ($expected as $slug => $featureKeys) {
            $manifest = $registry->findManifest($slug);

            self::assertNotNull($manifest, "Ferramenta oficial ausente: {$slug}");

            foreach ($featureKeys as $featureKey) {
                $feature = $manifest->feature($featureKey);

                self::assertNotNull($feature, "Feature de transparência ausente: {$slug}:{$featureKey}");
                self::assertSame(
                    ToolFeatureTier::Essential,
                    $feature->tier,
                    "Transparência necessária para conferir o resultado não pode ser Prazzu Plus: {$slug}:{$featureKey}",
                );
            }
        }
    }

    public function test_transparency_surfaces_are_not_conditioned_by_plus_access(): void
    {
        $pisCofins = file_get_contents(base_path('app/Tools/PisCofinsCalculator/Resources/views/index.blade.php'));
        $withholding = file_get_contents(base_path('app/Tools/InvoiceWithholdingCalculator/Resources/views/index.blade.php'));

        self::assertIsString($pisCofins);
        self::assertIsString($withholding);
        self::assertStringContainsString('data-essential-transparency="memory"', $pisCofins);
        self::assertStringNotContainsString('memoryAllowed', $pisCofins);
        self::assertStringContainsString('data-essential-transparency="memory"', $withholding);
        self::assertStringContainsString('data-essential-transparency="report"', $withholding);
        self::assertStringNotContainsString("plusAccess['memory']", $withholding);
        self::assertStringNotContainsString("plusAccess['report']", $withholding);
    }

    public function test_all_legacy_tool_pages_receive_the_same_post_result_continuity_entry_point(): void
    {
        $legacy = [
            'AccountingFeesCalculator' => 'calculadora-de-honorarios-contabeis',
            'BusinessDocumentValidator' => 'validador-de-cnpj',
            'FederalPaymentGuideGenerator' => 'gerador-darf-gps',
            'LaborTerminationCalculator' => 'calculadora-de-rescisao',
            'MarginMarkupCalculator' => 'calculadora-margem-markup',
            'ReceiptIssuer' => 'emissor-de-recibos',
            'VacationCalculator' => 'calculadora-ferias',
        ];

        foreach ($legacy as $module => $slug) {
            $view = file_get_contents(base_path("app/Tools/{$module}/Resources/views/index.blade.php"));

            self::assertIsString($view);
            self::assertStringContainsString(
                "<x-tools.plus-result-cta slug=\"{$slug}\" />",
                $view,
                "Página legada sem continuidade pós-resultado: {$module}",
            );
        }
    }

    public function test_shared_continuity_cta_can_resolve_history_without_page_specific_wiring(): void
    {
        $view = file_get_contents(resource_path('views/components/tools/plus-result-cta.blade.php'));

        self::assertIsString($view);
        self::assertStringContainsString('ToolCatalog::class', $view);
        self::assertStringContainsString("'.history.index'", $view);
        self::assertStringContainsString('supports_history', $view);
    }
}
