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
    public const SLUG = 'take-home-pay-calculator';

    public function integrations(): ToolIntegrationManifest
    {
        return new ToolIntegrationManifest(publishes: [], accepts: []);
    }

    public function analyticsJourney(): ToolAnalyticsJourney
    {
        return new ToolAnalyticsJourney(
            toolSlug: 'take-home-pay-calculator',
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
            name: 'Take-Home Pay Calculator',
            description: 'Estimate 2026 monthly take-home pay using federal income tax brackets, Social Security, Medicare, and user-entered deductions.',
            category: ToolCategory::Labor,
            icon: 'bi-cash-coin',
            routeName: 'tools.take-home-pay-calculator.index',
            vertical: 'contabilidade',
            version: '1.0.0',
            access: ToolAccess::Free,
            status: ToolStatus::Active,
            position: 5,
            featured: true,
            supportsHistory: true,
            keywords: ['take home pay', 'net pay calculator', 'federal tax', 'Social Security', 'Medicare'],
            capabilities: [
                ToolCapability::History,
                ToolCapability::VersionedPersistence,
                ToolCapability::Export,
            ],
            features: [
                new ToolFeature('calculate', 'Take-home pay with 2026 federal income-tax estimate and FICA', ToolFeatureTier::Essential),
                new ToolFeature('memory', 'Calculation details and sources normativas', ToolFeatureTier::Essential),
                new ToolFeature('variable_earnings', 'Custom taxable and non-taxable earnings', ToolFeatureTier::Plus),
                new ToolFeature('custom_discounts', 'Custom benefit and other deductions', ToolFeatureTier::Plus),
                new ToolFeature('history', 'Authenticated calculation history', ToolFeatureTier::Plus),
                new ToolFeature('export', 'CSV and print/PDF export', ToolFeatureTier::Plus),
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
        return 'tools-take-home-pay-calculator';
    }
}
