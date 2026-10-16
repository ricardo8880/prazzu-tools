<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator;

use App\Core\ToolIntegration\Data\ToolIntegrationManifest;
use App\Core\Tools\Analytics\Contracts\HasAnalyticsJourney;
use App\Core\Tools\Analytics\Data\ToolAnalyticsField;
use App\Core\Tools\Analytics\Data\ToolAnalyticsForm;
use App\Core\Tools\Analytics\Data\ToolAnalyticsJourney;
use App\Core\Tools\Api\Contracts\HasApiActions;
use App\Core\Tools\Contracts\HasMigrations;
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
use App\Tools\AccountingFeesCalculator\Api\Actions\CalculateApiAction;

final class Tool implements HasAnalyticsJourney, HasApiActions, HasHistoryPolicy, HasMigrations, HasToolIntegrations, HasViews, HasWebRoutes, ToolModule
{
    public function apiActions(): array
    {
        return [CalculateApiAction::class];
    }

    public function integrations(): ToolIntegrationManifest
    {
        return new ToolIntegrationManifest(
            publishes: ['company-operating-profile:v1'],
            accepts: ['company-tax-snapshot:v1'],
        );
    }

    public function analyticsJourney(): ToolAnalyticsJourney
    {
        return new ToolAnalyticsJourney(
            toolSlug: 'accounting-fee-calculator',
            forms: [
                new ToolAnalyticsForm(
                    key: 'main',
                    steps: ['input'],
                    fields: [
                        new ToolAnalyticsField('monthly_revenue', 'input', selector: '[name="monthly_revenue"]'),
                        new ToolAnalyticsField('employees', 'input', selector: '[name="employees"]'),
                        new ToolAnalyticsField('partners', 'input', selector: '[name="partners"]'),
                        new ToolAnalyticsField('tax_regime', 'input', selector: '[name="tax_regime"]'),
                        new ToolAnalyticsField('business_segment', 'input', selector: '[name="business_segment"]'),
                        new ToolAnalyticsField('monthly_invoices', 'input', selector: '[name="monthly_invoices"]'),
                        new ToolAnalyticsField('monthly_bank_transactions', 'input', selector: '[name="monthly_bank_transactions"]'),
                        new ToolAnalyticsField('complexity', 'input', selector: '[name="complexity"]'),
                        new ToolAnalyticsField('client_company', 'input', selector: '[name="client_company"]'),
                        new ToolAnalyticsField('client_document', 'input', selector: '[name="client_document"]'),
                        new ToolAnalyticsField('contact_name', 'input', selector: '[name="contact_name"]'),
                        new ToolAnalyticsField('accounting_firm', 'input', selector: '[name="accounting_firm"]'),
                        new ToolAnalyticsField('monthly_fee', 'input', selector: '[name="monthly_fee"]'),
                        new ToolAnalyticsField('setup_fee', 'input', selector: '[name="setup_fee"]'),
                        new ToolAnalyticsField('due_day', 'input', selector: '[name="due_day"]'),
                        new ToolAnalyticsField('validity_days', 'input', selector: '[name="validity_days"]'),
                        new ToolAnalyticsField('services', 'input', selector: '[name="services"]'),
                        new ToolAnalyticsField('notes', 'input', selector: '[name="notes"]'),
                        new ToolAnalyticsField('client_representative', 'input', selector: '[name="client_representative"]'),
                        new ToolAnalyticsField('accounting_firm_document', 'input', selector: '[name="accounting_firm_document"]'),
                        new ToolAnalyticsField('accounting_representative', 'input', selector: '[name="accounting_representative"]'),
                        new ToolAnalyticsField('start_date', 'input', selector: '[name="start_date"]'),
                        new ToolAnalyticsField('duration_months', 'input', selector: '[name="duration_months"]'),
                        new ToolAnalyticsField('adjustment_index', 'input', selector: '[name="adjustment_index"]'),
                        new ToolAnalyticsField('late_fee_percent', 'input', selector: '[name="late_fee_percent"]'),
                        new ToolAnalyticsField('termination_notice_days', 'input', selector: '[name="termination_notice_days"]'),
                        new ToolAnalyticsField('includes_data_privacy', 'input', selector: '[name="includes_data_privacy"]'),
                        new ToolAnalyticsField('includes_confidentiality', 'input', selector: '[name="includes_confidentiality"]'),
                        new ToolAnalyticsField('additional_terms', 'input', selector: '[name="additional_terms"]'),
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
            slug: 'accounting-fee-calculator',
            name: 'Accounting Fee Calculator',
            description: 'Estimate bookkeeping and accounting service fees based on client size, transaction volume, staffing, tax complexity, and service scope.',
            category: ToolCategory::Calculators,
            icon: 'bi-calculator',
            routeName: 'tools.accounting-fee-calculator.index',
            vertical: 'contabilidade',
            version: '1.2.0',
            access: ToolAccess::Free,
            status: ToolStatus::Active,
            position: 30,
            featured: true,
            supportsHistory: true,
            storesSensitiveData: true,
            keywords: ['accounting fees', 'bookkeeping fees', 'CPA pricing', 'bookkeeping pricing', 'monthly accounting fee'],
            capabilities: [
                ToolCapability::History,
                ToolCapability::VersionedPersistence,
                ToolCapability::Export,
                ToolCapability::SensitiveData,
                ToolCapability::PublishesIntegrations,
                ToolCapability::AcceptsIntegrations,
            ],
            features: [
                new ToolFeature('calculate', 'Complete accounting-fee estimate', ToolFeatureTier::Essential),
                new ToolFeature('adjust_fee', 'Complete fee adjustment', ToolFeatureTier::Essential),
                new ToolFeature('commercial_proposal', 'Commercial proposal generation', ToolFeatureTier::Plus),
                new ToolFeature('service_contract', 'Service agreement generation', ToolFeatureTier::Plus),
                new ToolFeature('history', 'History, favorites, and duplication', ToolFeatureTier::Plus),
                new ToolFeature('history_export', 'History export', ToolFeatureTier::Plus),
            ],
            persistence: new ToolPersistencePolicy(enabled: true, schemaVersion: 1, retentionDays: 365, minimumReadableSchemaVersion: 1),
            export: new ToolExportPolicy(enabled: true, formats: ['csv', 'json', 'pdf', 'print']),
            sharing: ToolSharingPolicy::disabled(),
            sensitiveData: new ToolSensitiveDataPolicy(SensitiveDataMode::Encrypted, ['history_payload']),
        );
    }

    public function historyPolicy(): ToolHistoryPolicy
    {
        return new ToolHistoryPolicy(
            enabled: true,
            retentionDays: 365,
            inputFields: [
                'run_type', 'monthly_revenue', 'employees', 'partners', 'monthly_invoices',
                'monthly_bank_transactions', 'tax_regime', 'business_segment',
                'complexity', 'scenario_label', 'index_type', 'reference_period',
                'current_value', 'percentage', 'notes',
            ],
            resultFields: [
                'minimum_fee', 'recommended_fee', 'upper_reference_fee',
                'complexity_score', 'complexity_level', 'rule_version',
                'current_value_cents', 'difference_cents', 'adjusted_value_cents',
            ],
            sensitiveFields: ['scenario_label', 'notes'],
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
        return 'tools-accounting-fee-calculator';
    }

    public function migrationsPath(): string
    {
        return __DIR__.'/Infrastructure/Database/Migrations';
    }
}
