<?php

declare(strict_types=1);

namespace App\Tools\EmploymentModelComparator;

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
use App\Core\Tools\History\Data\ToolHistoryPolicy;
use App\Core\Tools\Infrastructure\Data\ToolExportPolicy;
use App\Core\Tools\Infrastructure\Data\ToolPersistencePolicy;
use App\Core\Tools\Infrastructure\Data\ToolSensitiveDataPolicy;
use App\Core\Tools\Infrastructure\Data\ToolSharingPolicy;

final class Tool implements HasAnalyticsJourney, HasHistoryPolicy, HasToolIntegrations, HasViews, HasWebRoutes, ToolModule
{
    public function integrations(): ToolIntegrationManifest
    {
        return new ToolIntegrationManifest(
            publishes: [],
            accepts: [],
        );
    }

    public function analyticsJourney(): ToolAnalyticsJourney
    {
        return new ToolAnalyticsJourney(
            toolSlug: 'employee-vs-contractor-calculator',
            forms: [
                new ToolAnalyticsForm(
                    key: 'main',
                    steps: ['input'],
                    fields: [
                        new ToolAnalyticsField('w2_gross', 'input', selector: '[name="w2_gross"]'),
                        new ToolAnalyticsField('w2_benefits', 'input', selector: '[name="w2_benefits"]'),
                        new ToolAnalyticsField('w2_employee_deductions', 'input', selector: '[name="w2_employee_deductions"]'),
                        new ToolAnalyticsField('w2_employer_burden', 'input', selector: '[name="w2_employer_burden"]'),
                        new ToolAnalyticsField('contractor_revenue', 'input', selector: '[name="contractor_revenue"]'),
                        new ToolAnalyticsField('contractor_tax_rate', 'input', selector: '[name="contractor_tax_rate"]'),
                        new ToolAnalyticsField('contractor_expenses', 'input', selector: '[name="contractor_expenses"]'),
                        new ToolAnalyticsField('self_employed_revenue', 'input', selector: '[name="self_employed_revenue"]'),
                        new ToolAnalyticsField('self_employed_tax_rate', 'input', selector: '[name="self_employed_tax_rate"]'),
                        new ToolAnalyticsField('self_employed_expenses', 'input', selector: '[name="self_employed_expenses"]'),
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
            slug: 'employee-vs-contractor-calculator',
            name: 'W-2 vs 1099 Contractor Calculator',
            description: 'Compare estimated worker take-home amounts and payer costs for W-2 employment, 1099 contracting, and self-employment scenarios.',
            category: ToolCategory::Labor,
            icon: 'bi-arrow-left-right',
            routeName: 'tools.employee-vs-contractor-calculator.index',
            vertical: 'contabilidade',
            version: '1.0.0',
            access: ToolAccess::Free,
            status: ToolStatus::Beta,
            position: 150,
            supportsHistory: true,
            storesSensitiveData: false,
            keywords: ['W-2 vs 1099', 'contractor calculator', 'employee vs contractor', 'self employed'],
            capabilities: [
                ToolCapability::History,
                ToolCapability::VersionedPersistence,
                ToolCapability::Export,
            ],
            features: [
                new ToolFeature('calculate', 'Full calculation', ToolFeatureTier::Essential),
                new ToolFeature('spreadsheet_export', 'Advanced Excel export', ToolFeatureTier::Plus),
            ],
            persistence: new ToolPersistencePolicy(enabled: true, schemaVersion: 1, retentionDays: 365, minimumReadableSchemaVersion: 1),
            export: new ToolExportPolicy(enabled: true, formats: ['csv', 'json', 'pdf', 'xlsx']),
            sharing: ToolSharingPolicy::disabled(),
            sensitiveData: ToolSensitiveDataPolicy::none(),
        );
    }

    public function historyPolicy(): ToolHistoryPolicy
    {
        return new ToolHistoryPolicy(enabled: true, retentionDays: 365, inputFields: ['w2_gross', 'w2_benefits', 'w2_employee_deductions', 'w2_employer_burden', 'contractor_revenue', 'contractor_tax_rate', 'contractor_expenses', 'self_employed_revenue', 'self_employed_tax_rate', 'self_employed_expenses'], resultFields: ['w2_net', 'contractor_net', 'self_employed_net', 'highest_net'], sensitiveFields: []);
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
        return 'tools-employee-vs-contractor-calculator';
    }
}
