<?php

declare(strict_types=1);

namespace App\Tools\NetSalaryCalculator;

use App\Core\ToolIntegration\Data\ToolIntegrationManifest;
use App\Core\Tools\Analytics\Contracts\HasAnalyticsJourney;
use App\Core\Tools\Analytics\Data\ToolAnalyticsField;
use App\Core\Tools\Analytics\Data\ToolAnalyticsForm;
use App\Core\Tools\Analytics\Data\ToolAnalyticsJourney;
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
use App\Core\Tools\History\Contracts\ProvidesHistoryContext;
use App\Core\Tools\History\Data\ToolHistoryPolicy;
use App\Core\Tools\History\Support\HistoryPeriodFormatter;
use App\Core\Tools\Infrastructure\Data\ToolExportPolicy;
use App\Core\Tools\Infrastructure\Data\ToolPersistencePolicy;
use App\Core\Tools\Infrastructure\Data\ToolSensitiveDataPolicy;
use App\Core\Tools\Infrastructure\Data\ToolSharingPolicy;
use DateTimeImmutable;

final class Tool implements HasAnalyticsJourney, HasHistoryPolicy, ProvidesHistoryContext, HasToolIntegrations, HasViews, HasWebRoutes, ToolModule
{
    public const SLUG = 'calculadora-salario-liquido';

    public function integrations(): ToolIntegrationManifest
    {
        return new ToolIntegrationManifest(publishes: [], accepts: []);
    }

    public function analyticsJourney(): ToolAnalyticsJourney
    {
        return new ToolAnalyticsJourney(
            toolSlug: 'calculadora-salario-liquido',
            forms: [
                new ToolAnalyticsForm(
                    key: 'main',
                    steps: ['input'],
                    fields: [
                        new ToolAnalyticsField('competence', 'input', selector: '[name="competence"]'),
                        new ToolAnalyticsField('base_salary', 'input', selector: '[name="base_salary"]'),
                        new ToolAnalyticsField('dependents', 'input', selector: '[name="dependents"]'),
                        new ToolAnalyticsField('taxable_additional_earnings', 'input', selector: '[name="taxable_additional_earnings"]'),
                        new ToolAnalyticsField('non_taxable_earnings', 'input', selector: '[name="non_taxable_earnings"]'),
                        new ToolAnalyticsField('judicial_pension', 'input', selector: '[name="judicial_pension"]'),
                        new ToolAnalyticsField('transport_discount', 'input', selector: '[name="transport_discount"]'),
                        new ToolAnalyticsField('meal_discount', 'input', selector: '[name="meal_discount"]'),
                        new ToolAnalyticsField('health_plan_discount', 'input', selector: '[name="health_plan_discount"]'),
                        new ToolAnalyticsField('other_discounts', 'input', selector: '[name="other_discounts"]'),
                        new ToolAnalyticsField('confirm_assumptions', 'input', selector: '[name="confirm_assumptions"]'),
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
            name: 'Calculadora de Salário Líquido',
            description: 'Calcule salário líquido CLT com INSS e IRRF de 2026, dependentes, pensão e descontos, com memória transparente.',
            category: ToolCategory::Labor,
            icon: 'bi-cash-coin',
            routeName: 'tools.calculadora-salario-liquido.index',
            vertical: 'contabilidade',
            version: '1.0.0',
            access: ToolAccess::Free,
            status: ToolStatus::Active,
            position: 5,
            featured: true,
            supportsHistory: true,
            keywords: ['salário líquido', 'salario liquido', 'INSS 2026', 'IRRF 2026', 'salário bruto', 'folha de pagamento'],
            capabilities: [
                ToolCapability::History,
                ToolCapability::VersionedPersistence,
                ToolCapability::Export,
            ],
            features: [
                new ToolFeature('calculate', 'Salário líquido com INSS, IRRF e descontos', ToolFeatureTier::Essential),
                new ToolFeature('memory', 'Memória de cálculo e fontes normativas', ToolFeatureTier::Essential),
                new ToolFeature('variable_earnings', 'Proventos tributáveis e não tributáveis personalizados', ToolFeatureTier::Plus),
                new ToolFeature('custom_discounts', 'Benefícios e descontos personalizados', ToolFeatureTier::Plus),
                new ToolFeature('history', 'Histórico autenticado de cálculos', ToolFeatureTier::Plus),
                new ToolFeature('export', 'Exportação CSV e relatório para impressão/PDF', ToolFeatureTier::Plus),
            ],
            persistence: new ToolPersistencePolicy(enabled: true, schemaVersion: 1, retentionDays: 365, minimumReadableSchemaVersion: 1),
            export: new ToolExportPolicy(enabled: true, formats: ['csv', 'json', 'pdf']),
            sharing: ToolSharingPolicy::disabled(),
            sensitiveData: ToolSensitiveDataPolicy::none(),
        );
    }

    public function historyPolicy(): ToolHistoryPolicy
    {
        return new ToolHistoryPolicy(
            enabled: true,
            retentionDays: 365,
            inputFields: ['competence', 'base_salary', 'taxable_additional_earnings', 'non_taxable_earnings', 'dependents', 'judicial_pension', 'transport_discount', 'meal_discount', 'health_plan_discount', 'other_discounts'],
            resultFields: ['gross', 'inss', 'irrf', 'discounts', 'net', 'summary', 'details', 'warnings', 'calculation_memory'],
            sensitiveFields: [],
        );
    }

    public function historyContext(array $input, DateTimeImmutable $referenceDate): ?string
    {
        return HistoryPeriodFormatter::yearMonth($input['competence'] ?? null);
    }

    public function webRoutesPath(): string
    {
        return __DIR__.'/Routes/web.php';
    }

    public function viewsPath(): string
    {
        return __DIR__.'/Resources/views';
    }

    public function viewsNamespace(): string
    {
        return 'tools-calculadora-salario-liquido';
    }
}
