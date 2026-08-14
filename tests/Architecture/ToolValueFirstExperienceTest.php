<?php

declare(strict_types=1);

namespace Tests\Architecture;

use Tests\TestCase;

final class ToolValueFirstExperienceTest extends TestCase
{
    public function test_shared_tool_page_renders_feature_tiers_after_tool_content(): void
    {
        $view = file_get_contents(base_path('resources/views/components/tools/page.blade.php'));

        self::assertIsString($view);
        self::assertGreaterThan(strpos($view, '{{ $slot }}'), strpos($view, '<x-tool-feature-tiers'));
    }

    public function test_feature_tiers_are_compact_and_collapsible(): void
    {
        $view = file_get_contents(base_path('resources/views/components/tool-feature-tiers.blade.php'));

        self::assertIsString($view);
        self::assertStringContainsString('data-tool-feature-tiers', $view);
        self::assertStringContainsString('<details', $view);
        self::assertStringContainsString('<summary', $view);
    }

    public function test_legacy_tool_pages_no_longer_render_feature_tiers_before_the_main_form(): void
    {
        $views = glob(base_path('app/Tools/*/Resources/views/index.blade.php'));

        self::assertIsArray($views);

        foreach ($views as $path) {
            $view = file_get_contents($path);

            self::assertIsString($view);

            if (! str_contains($view, '<x-tool-feature-tiers') || str_contains($view, '<x-tools.page')) {
                continue;
            }

            $tiersPosition = strpos($view, '<x-tool-feature-tiers');
            $formPosition = strpos($view, 'data-testid="tool-form-panel"');

            self::assertNotFalse($formPosition, $path.' precisa manter o marcador do formulário principal.');
            self::assertGreaterThan($formPosition, $tiersPosition, $path.' deve priorizar a tarefa antes dos tiers comerciais.');
        }
    }

    public function test_shared_result_panel_exposes_prominent_result_hook(): void
    {
        $view = file_get_contents(base_path('resources/views/components/tools/result-panel.blade.php'));

        self::assertIsString($view);
        self::assertStringContainsString('prazzu-tool-result-panel', $view);
        self::assertStringContainsString('data-tool-result-panel', $view);
        self::assertStringContainsString("'eyebrow' => 'Seu resultado'", $view);
    }


    public function test_next_steps_are_hidden_until_the_tool_has_delivered_a_result(): void
    {
        $view = file_get_contents(base_path('resources/views/components/tools/page.blade.php'));
        $script = file_get_contents(resource_path('js/app.js'));

        self::assertIsString($view);
        self::assertIsString($script);
        self::assertStringContainsString('data-tool-next-steps hidden', $view);
        self::assertStringContainsString('revealToolNextSteps', $script);
        self::assertStringContainsString("querySelector('[data-tool-next-steps]')", $script);
    }

    public function test_custom_result_views_expose_the_shared_result_surface_contract(): void
    {
        $modules = [
            'AccountingFeesCalculator',
            'BusinessDocumentValidator',
            'FederalPaymentGuideGenerator',
            'LaborTerminationCalculator',
            'MarginMarkupCalculator',
            'ProLaboreSimulator',
            'ProfitDistributionCalculator',
            'ReceiptIssuer',
            'SimplesNacionalCalculator',
            'TaxRegimeComparator',
            'VacationCalculator',
        ];

        foreach ($modules as $module) {
            $view = file_get_contents(base_path("app/Tools/{$module}/Resources/views/index.blade.php"));

            self::assertIsString($view);
            self::assertStringContainsString('data-tool-result-panel', $view, $module.' precisa expor a superfície compartilhada de resultado.');
        }
    }

    public function test_frontend_positions_tiers_after_result_and_focuses_generated_result(): void
    {
        $script = file_get_contents(resource_path('js/app.js'));

        self::assertIsString($script);
        self::assertStringContainsString('positionFeatureTiersAfterResult', $script);
        self::assertStringContainsString("scrollIntoView({ behavior: reducedMotion ? 'auto' : 'smooth', block: 'start' })", $script);
        self::assertStringContainsString("focus({ preventScroll: true })", $script);
    }
}
