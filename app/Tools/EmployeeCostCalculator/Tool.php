<?php

declare(strict_types=1);

namespace App\Tools\EmployeeCostCalculator;

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
use App\Core\Tools\Infrastructure\Enums\SensitiveDataMode;

final class Tool implements HasAnalyticsJourney, HasHistoryPolicy, HasToolIntegrations, HasViews, HasWebRoutes, ToolModule
{
    public const SLUG = 'employee-cost-calculator';

    public function integrations(): ToolIntegrationManifest
    {
        return new ToolIntegrationManifest(publishes: [], accepts: []);
    }

    public function analyticsJourney(): ToolAnalyticsJourney
    {
        return new ToolAnalyticsJourney(
            toolSlug: 'employee-cost-calculator',
            forms: [
                new ToolAnalyticsForm(
                    key: 'main',
                    steps: ['input'],
                    fields: [
                        new ToolAnalyticsField('employee_name', 'input', selector: '[name="employee_name"]'),
                        new ToolAnalyticsField('department', 'input', selector: '[name="department"]'),
                        new ToolAnalyticsField('company_profile_id', 'input', selector: '[name="company_profile_id"]'),
                        new ToolAnalyticsField('scenario_name', 'input', selector: '[name="scenario_name"]'),
                        new ToolAnalyticsField('futa_profile', 'input', selector: '[name="futa_profile"]'),
                        new ToolAnalyticsField('suta_rate', 'input', selector: '[name="suta_rate"]'),
                        new ToolAnalyticsField('other_employer_cost_rate', 'input', selector: '[name="other_employer_cost_rate"]'),
                        new ToolAnalyticsField('monthly_hours', 'input', selector: '[name="monthly_hours"]'),
                        new ToolAnalyticsField('import_file', 'input', selector: '[name="import_file"]'),
                        new ToolAnalyticsField('import_token', 'input', selector: '[name="import_token"]'),
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
            name: 'Employee Cost Calculator',
            description: 'Estimate annual and monthly W-2 employee cost including wages, benefits, employer FICA, FUTA, SUTA, and other employer costs.',
            category: ToolCategory::Labor,
            icon: 'bi-person-vcard',
            routeName: 'tools.employee-cost-calculator.index',
            vertical: 'contabilidade',
            version: '1.1.0',
            access: ToolAccess::Free,
            status: ToolStatus::Beta,
            position: 110,
            supportsHistory: true,
            storesSensitiveData: true,
            keywords: ['employee cost', 'W-2 employee cost', 'employer payroll taxes', 'FICA', 'FUTA', 'SUTA'],
            capabilities: [
                ToolCapability::History,
                ToolCapability::VersionedPersistence,
                ToolCapability::Export,
                ToolCapability::SensitiveData,
            ],
            features: [
                new ToolFeature('calculate', 'Individual employer-cost estimate and calculation details', ToolFeatureTier::Essential),
                new ToolFeature('print_report', 'Printable individual employer-cost report', ToolFeatureTier::Essential),
                new ToolFeature('history', 'Saved calculation history and reuse', ToolFeatureTier::Plus),
                new ToolFeature('company_profiles', 'Reusable company profiles', ToolFeatureTier::Plus),
                new ToolFeature('employee_profiles', 'Reusable employee profiles', ToolFeatureTier::Plus),
                new ToolFeature('batch_processing', 'Batch employee-cost calculations', ToolFeatureTier::Plus),
                new ToolFeature('csv_import', 'Validated employee import from CSV', ToolFeatureTier::Plus),
                new ToolFeature('xlsx_import', 'Validated employee import from XLSX', ToolFeatureTier::Plus),
                new ToolFeature('scenarios', 'Scenario creation and comparison', ToolFeatureTier::Plus),
                new ToolFeature('employment_model_comparison', 'W-2 employee vs 1099 contractor cost comparison', ToolFeatureTier::Plus),
                new ToolFeature('csv_export', 'CSV report export', ToolFeatureTier::Plus),
                new ToolFeature('xlsx_export', 'XLSX report export', ToolFeatureTier::Plus),
                new ToolFeature('professional_report', 'Consolidated and department reports', ToolFeatureTier::Plus),
                new ToolFeature('branded_report', 'Branded report with company profile data', ToolFeatureTier::Plus),
                new ToolFeature('projections', 'Monthly and annual cost projections', ToolFeatureTier::Plus),
            ],
            persistence: new ToolPersistencePolicy(
                enabled: true,
                schemaVersion: 1,
                retentionDays: 365,
                minimumReadableSchemaVersion: 1,
            ),
            export: new ToolExportPolicy(enabled: true, formats: ['csv', 'json', 'pdf', 'xlsx']),
            sharing: ToolSharingPolicy::disabled(),
            sensitiveData: new ToolSensitiveDataPolicy(
                SensitiveDataMode::Encrypted,
                ['employee_name', 'employees', 'document'],
            ),
        );
    }

    public function historyPolicy(): ToolHistoryPolicy
    {
        return new ToolHistoryPolicy(
            enabled: true,
            retentionDays: 365,
            inputFields: [
                'employee_name',
                'department',
                'scenario_name',
                'company_profile_id',
                'salary',
                'variable_pay',
                'benefits',
                'futa_profile',
                'suta_rate',
                'other_employer_cost_rate',
                'monthly_hours',
                'employees',
            ],
            resultFields: ['calculation_type', 'result', 'batch'],
            sensitiveFields: ['employee_name', 'employees'],
        );
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
        return 'tools-employee-cost-calculator';
    }
}
