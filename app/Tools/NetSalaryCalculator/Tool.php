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
                        new ToolAnalyticsField('taxable_additional_earnings', 'input', selector: '[name="taxable_additional_earnings"]'),
                        new ToolAnalyticsField('non_taxable_earnings', 'input', selector: '[name="non_taxable_earnings"]'),
                        new ToolAnalyticsField('filing_status', 'input', selector: '[name="filing_status"]'),
                        new ToolAnalyticsField('pretax_deductions', 'input', selector: '[name="pretax_deductions"]'),
                        new ToolAnalyticsField('state_local_withholding', 'input', selector: '[name="state_local_withholding"]'),
                        new ToolAnalyticsField('additional_federal_withholding', 'input', selector: '[name="additional_federal_withholding"]'),
                        new ToolAnalyticsField('benefit_deductions', 'input', selector: '[name="benefit_deductions"]'),
                        new ToolAnalyticsField('other_deductions', 'input', selector: '[name="other_deductions"]'),
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
                new ToolFeature('memory', 'Calculation details and source assumptions', ToolFeatureTier::Essential),
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
            inputFields: ['competence', 'base_salary', 'taxable_additional_earnings', 'non_taxable_earnings', 'filing_status', 'pretax_deductions', 'state_local_withholding', 'additional_federal_withholding', 'benefit_deductions', 'other_deductions'],
            resultFields: ['gross_minor', 'federal_income_tax_minor', 'social_security_minor', 'medicare_minor', 'additional_medicare_minor', 'deductions_minor', 'net_minor', 'summary', 'details', 'warnings', 'calculation_memory'],
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
