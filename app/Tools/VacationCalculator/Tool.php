<?php

declare(strict_types=1);

namespace App\Tools\VacationCalculator;

use App\Core\Tools\Analytics\Contracts\HasAnalyticsJourney;
use App\Core\Tools\Analytics\Data\ToolAnalyticsField;
use App\Core\Tools\Analytics\Data\ToolAnalyticsForm;
use App\Core\Tools\Analytics\Data\ToolAnalyticsJourney;
use App\Core\Tools\Api\Contracts\HasApiActions;
use App\Core\ToolIntegration\Data\ToolIntegrationManifest;
use App\Core\Tools\Contracts\HasToolIntegrations;
use App\Core\Tools\Contracts\HasViews;
use App\Core\Tools\Contracts\HasWebRoutes;
use App\Core\Tools\Contracts\ToolModule;
use App\Core\Tools\Data\ToolFeature;
use App\Core\Tools\Data\ToolManifest;
use App\Core\Tools\Enums\ToolAccess;
use App\Core\Tools\Enums\ToolCapability;
use App\Core\Tools\Enums\ToolCategory;
use App\Core\Tools\Enums\ToolFeatureTier;
use App\Core\Tools\Enums\ToolStatus;
use App\Core\Tools\History\Contracts\HasHistoryPolicy;
use App\Core\Tools\History\Data\ToolHistoryPolicy;
use App\Core\Tools\Infrastructure\Data\ToolExportPolicy;
use App\Core\Tools\Infrastructure\Data\ToolPersistencePolicy;
use App\Core\Tools\Infrastructure\Data\ToolSensitiveDataPolicy;
use App\Core\Tools\Infrastructure\Data\ToolSharingPolicy;

use App\Tools\VacationCalculator\Api\Actions\CalculateApiAction;

final class Tool implements HasAnalyticsJourney, HasApiActions, HasHistoryPolicy, HasToolIntegrations, HasViews, HasWebRoutes, ToolModule
{
    public function apiActions(): array
    {
        return [CalculateApiAction::class];
    }

    public const SLUG = 'calculadora-ferias';

    public function integrations(): ToolIntegrationManifest
    {
        return new ToolIntegrationManifest(publishes: [], accepts: []);
    }

    public function analyticsJourney(): ToolAnalyticsJourney
    {
        return new ToolAnalyticsJourney(
            toolSlug: 'calculadora-ferias',
            forms: [
                new ToolAnalyticsForm(
                    key: 'main',
                    steps: ['input'],
                    fields: [
                    new ToolAnalyticsField('monthly_salary', 'input', selector: '[name="monthly_salary"]'),
                    new ToolAnalyticsField('acquisition_start_date', 'input', selector: '[name="acquisition_start_date"]'),
                    new ToolAnalyticsField('vacation_start_date', 'input', selector: '[name="vacation_start_date"]'),
                    new ToolAnalyticsField('unjustified_absences', 'input', selector: '[name="unjustified_absences"]'),
                    new ToolAnalyticsField('convert_one_third_to_cash', 'input', selector: '[name="convert_one_third_to_cash"]'),
                    ],
                    actions: ['calculate', 'export', 'share'],
                    selector: 'form[action*="calculate"]',
                    resultSelector: '[data-analytics-result="main"]',
                ),
            ],
        );
    }

    public function manifest(): ToolManifest
    {
        return new ToolManifest(
            slug: self::SLUG,
            name: 'Calculadora de Férias',
            description: 'Calcule dias de direito, remuneração de férias, terço constitucional, abono pecuniário e prazos com memória transparente.',
            category: ToolCategory::Labor,
            icon: 'bi-calendar2-check',
            routeName: 'tools.calculadora-ferias.index',
            vertical: 'contabilidade',
            version: '1.0.0',
            access: ToolAccess::Free,
            status: ToolStatus::Active,
            position: 35,
            featured: false,
            supportsHistory: true,
            storesSensitiveData: false,
            keywords: ['férias', 'calcular férias', 'abono pecuniário', 'um terço de férias', 'período aquisitivo', 'período concessivo'],
            capabilities: [ToolCapability::History, ToolCapability::VersionedPersistence, ToolCapability::Export],
            features: [
                new ToolFeature('vacation_calculation', 'Cálculo de férias, terço constitucional e descontos informados', ToolFeatureTier::Essential),
                new ToolFeature('cash_allowance', 'Conversão de um terço do período em abono pecuniário', ToolFeatureTier::Essential),
                new ToolFeature('period_deadlines', 'Períodos aquisitivo e concessivo e prazo de pagamento', ToolFeatureTier::Essential),
                new ToolFeature('calculation_memory', 'Memória detalhada das bases, dias e valores', ToolFeatureTier::Essential),
                new ToolFeature('history', 'Histórico, recuperação e duplicação de cálculos', ToolFeatureTier::Plus),
                new ToolFeature('multiple_employees', 'Cálculo e planejamento para múltiplos funcionários', ToolFeatureTier::Plus),
                new ToolFeature('vacation_planning', 'Calendário, alertas e planejamento de períodos', ToolFeatureTier::Plus),
                new ToolFeature('professional_export', 'Exportação profissional em PDF, XLSX, CSV e JSON', ToolFeatureTier::Plus),
            ],
            persistence: new ToolPersistencePolicy(enabled: true, schemaVersion: 1, retentionDays: 180, minimumReadableSchemaVersion: 1),
            export: new ToolExportPolicy(enabled: true, formats: ['pdf', 'xlsx', 'csv', 'json']),
            sharing: ToolSharingPolicy::disabled(),
            sensitiveData: ToolSensitiveDataPolicy::none(),
        );
    }

    public function historyPolicy(): ToolHistoryPolicy
    {
        return new ToolHistoryPolicy(
            enabled: true,
            retentionDays: 180,
            inputFields: ['monthly_salary', 'acquisition_start_date', 'vacation_start_date', 'unjustified_absences', 'convert_one_third_to_cash', 'commission_average', 'overtime_average', 'recurring_additions', 'other_deductions'],
            resultFields: ['tool_slug', 'schema_version', 'summary', 'details', 'warnings', 'next_actions'],
            sensitiveFields: [],
        );
    }

    public function webRoutesPath(): string { return __DIR__.'/Routes/web.php'; }
    public function viewsPath(): string { return __DIR__.'/Resources/views'; }
    public function viewsNamespace(): string { return 'tools-calculadora-ferias'; }
}
