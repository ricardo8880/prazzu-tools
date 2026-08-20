<?php

declare(strict_types=1);

namespace Tests\Architecture;

use Tests\TestCase;

final class ToolUxHardeningLot5Test extends TestCase
{
    public function test_shared_form_fields_expose_validation_errors_to_assistive_technology(): void
    {
        foreach (['input', 'select'] as $component) {
            $view = file_get_contents(base_path("resources/views/components/tools/form/{$component}.blade.php"));

            self::assertIsString($view);
            self::assertStringContainsString('aria-invalid="true"', $view, $component.' deve sinalizar erro semanticamente.');
            self::assertStringContainsString("\$fieldId.'-error'", $view, $component.' deve relacionar o campo à mensagem de erro.');
            self::assertStringContainsString('aria-describedby', $view, $component.' deve expor erro/ajuda por aria-describedby.');
        }
    }

    public function test_validation_summary_is_programmatically_focusable(): void
    {
        $view = file_get_contents(base_path('resources/views/components/tools/validation-summary.blade.php'));

        self::assertIsString($view);
        self::assertStringContainsString('data-testid', $view);
        self::assertStringContainsString('tabindex="-1"', $view);
        self::assertStringContainsString('role="alert"', $view);
    }

    public function test_legacy_validation_summaries_follow_the_shared_focus_contract(): void
    {
        $modules = [
            'FederalPaymentGuideGenerator',
            'MarginMarkupCalculator',
            'ReceiptIssuer',
            'AccountingFeesCalculator',
            'VacationCalculator',
            'LaborTerminationCalculator',
        ];

        foreach ($modules as $module) {
            $view = file_get_contents(base_path("app/Tools/{$module}/Resources/views/index.blade.php"));

            self::assertIsString($view);
            self::assertStringContainsString('data-testid="validation-summary"', $view, $module.' deve expor o resumo de validação ao comportamento compartilhado.');
            self::assertStringContainsString('tabindex="-1"', $view, $module.' deve permitir foco programático no resumo de validação.');
        }
    }

    public function test_tool_post_forms_have_shared_processing_feedback_and_double_submit_protection(): void
    {
        $script = file_get_contents(resource_path('js/app.js'));

        self::assertIsString($script);
        self::assertStringContainsString('markToolFormSubmitting', $script);
        self::assertStringContainsString("form.setAttribute('aria-busy', 'true')", $script);
        self::assertStringContainsString("status.setAttribute('role', 'status')", $script);
        self::assertStringContainsString("form.dataset.toolSubmitting === 'true'", $script);
        self::assertStringContainsString("event.preventDefault();", $script);
        self::assertStringContainsString('Processando sua solicitação. Aguarde o resultado.', $script);
    }

    public function test_server_validation_focuses_the_shared_summary(): void
    {
        $script = file_get_contents(resource_path('js/app.js'));

        self::assertIsString($script);
        self::assertStringContainsString('focusValidationSummary', $script);
        self::assertStringContainsString("querySelector('[data-testid=\"validation-summary\"]')", $script);
        self::assertStringContainsString("summary.focus({ preventScroll: true })", $script);
    }

    public function test_all_tool_tables_are_mobile_responsive(): void
    {
        $views = glob(base_path('app/Tools/*/Resources/views/index.blade.php'));

        self::assertIsArray($views);

        foreach ($views as $path) {
            $view = file_get_contents($path);

            self::assertIsString($view);
            $tables = substr_count($view, '<table');

            if ($tables === 0) {
                continue;
            }

            self::assertGreaterThanOrEqual(
                $tables,
                substr_count($view, 'table-responsive'),
                $path.' possui tabela sem proteção responsiva para telas estreitas.',
            );
        }
    }
}
