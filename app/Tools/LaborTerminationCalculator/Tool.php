<?php

declare(strict_types=1);

namespace App\Tools\LaborTerminationCalculator;

use App\Core\ToolIntegration\Data\ToolIntegrationManifest;
use App\Core\Tools\Analytics\Contracts\HasAnalyticsJourney;
use App\Core\Tools\Analytics\Data\ToolAnalyticsField;
use App\Core\Tools\Analytics\Data\ToolAnalyticsForm;
use App\Core\Tools\Analytics\Data\ToolAnalyticsJourney;
use App\Core\Tools\Api\Contracts\HasApiActions;
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
use App\Tools\LaborTerminationCalculator\Api\Actions\CalculateApiAction;

final class Tool implements HasAnalyticsJourney, HasApiActions, HasHistoryPolicy, HasToolIntegrations, HasViews, HasWebRoutes, ToolModule
{
    public const SLUG = 'final-pay-severance-calculator';

    public function apiActions(): array
    {
        return [CalculateApiAction::class];
    }

    /** @var array<int, string> */
    private const HISTORY_INPUT_FIELDS = [
        'termination_date', 'separation_type', 'hourly_rate', 'regular_hours_due', 'unused_pto_hours',
        'pto_payout_percent', 'weekly_base_pay', 'severance_weeks', 'bonus_commission_due', 'other_pay',
        'federal_withholding', 'state_local_withholding', 'other_deductions',
    ];

    /** @var array<int, string> */
    private const HISTORY_RESULT_FIELDS = [
        'termination_date', 'separation_type', 'regular_pay_due', 'pto_payout', 'severance_pay',
        'bonus_commission_due', 'other_pay', 'gross_final_pay', 'federal_withholding',
        'state_local_withholding', 'other_deductions', 'total_deductions', 'net_final_pay', 'net_total',
        'warnings', 'rule_version', 'calculation_memory',
    ];

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
            toolSlug: 'final-pay-severance-calculator',
            forms: [
                new ToolAnalyticsForm(
                    key: 'main',
                    steps: ['input'],
                    fields: [
                        new ToolAnalyticsField('termination_date', 'input', selector: '[name="termination_date"]'),
                        new ToolAnalyticsField('separation_type', 'input', selector: '[name="separation_type"]'),
                        new ToolAnalyticsField('hourly_rate', 'input', selector: '[name="hourly_rate"]'),
                        new ToolAnalyticsField('regular_hours_due', 'input', selector: '[name="regular_hours_due"]'),
                        new ToolAnalyticsField('unused_pto_hours', 'input', selector: '[name="unused_pto_hours"]'),
                        new ToolAnalyticsField('pto_payout_percent', 'input', selector: '[name="pto_payout_percent"]'),
                        new ToolAnalyticsField('weekly_base_pay', 'input', selector: '[name="weekly_base_pay"]'),
                        new ToolAnalyticsField('severance_weeks', 'input', selector: '[name="severance_weeks"]'),
                        new ToolAnalyticsField('bonus_commission_due', 'input', selector: '[name="bonus_commission_due"]'),
                        new ToolAnalyticsField('other_pay', 'input', selector: '[name="other_pay"]'),
                        new ToolAnalyticsField('federal_withholding', 'input', selector: '[name="federal_withholding"]'),
                        new ToolAnalyticsField('state_local_withholding', 'input', selector: '[name="state_local_withholding"]'),
                        new ToolAnalyticsField('other_deductions', 'input', selector: '[name="other_deductions"]'),
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
            slug: 'final-pay-severance-calculator',
            name: 'Final Pay & Severance Calculator',
            description: 'Estimate final wages, PTO payout, severance, bonuses, withholding, and deductions when employment ends.',
            category: ToolCategory::Labor,
            icon: 'bi-briefcase',
            routeName: 'tools.final-pay-severance-calculator.index',
            vertical: 'contabilidade',
            version: '1.0.0',
            access: ToolAccess::Free,
            status: ToolStatus::Active,
            position: 10,
            featured: true,
            supportsHistory: true,
            storesSensitiveData: true,
            keywords: ['final pay', 'severance calculator', 'PTO payout', 'termination pay', 'layoff pay'],
            capabilities: [
                ToolCapability::History,
                ToolCapability::VersionedPersistence,
                ToolCapability::Export,
                ToolCapability::SensitiveData,
            ],
            features: [
                new ToolFeature('calculate', 'Final-pay and severance estimate', ToolFeatureTier::Essential),
                new ToolFeature('current_report', 'Current calculation report', ToolFeatureTier::Essential),
                new ToolFeature('history', 'Calculation history', ToolFeatureTier::Plus),
                new ToolFeature('repeat_history', 'Reuse saved calculation', ToolFeatureTier::Plus),
                new ToolFeature('historical_report', 'Saved calculation report', ToolFeatureTier::Plus),
            ],
            persistence: new ToolPersistencePolicy(enabled: true, schemaVersion: 1, retentionDays: 180, minimumReadableSchemaVersion: 1),
            export: new ToolExportPolicy(enabled: true, formats: ['csv', 'json', 'pdf', 'print']),
            sharing: ToolSharingPolicy::disabled(),
            sensitiveData: new ToolSensitiveDataPolicy(SensitiveDataMode::Encrypted, ['history_payload']),
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
        return 'tools-final-pay-severance-calculator';
    }

    public function historyPolicy(): ToolHistoryPolicy
    {
        return new ToolHistoryPolicy(
            enabled: true,
            retentionDays: 180,
            inputFields: self::HISTORY_INPUT_FIELDS,
            resultFields: self::HISTORY_RESULT_FIELDS,
            sensitiveFields: self::HISTORY_INPUT_FIELDS,
        );
    }
}
