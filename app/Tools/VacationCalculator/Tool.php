<?php

declare(strict_types=1);

namespace App\Tools\VacationCalculator;

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
use App\Tools\VacationCalculator\Api\Actions\CalculateApiAction;

final class Tool implements HasAnalyticsJourney, HasApiActions, HasHistoryPolicy, HasToolIntegrations, HasViews, HasWebRoutes, ToolModule
{
    public function apiActions(): array
    {
        return [CalculateApiAction::class];
    }

    public const SLUG = 'pto-calculator';

    public function integrations(): ToolIntegrationManifest
    {
        return new ToolIntegrationManifest(publishes: [], accepts: []);
    }

    public function analyticsJourney(): ToolAnalyticsJourney
    {
        return new ToolAnalyticsJourney(
            toolSlug: 'pto-calculator',
            forms: [
                new ToolAnalyticsForm(
                    key: 'main',
                    steps: ['input'],
                    fields: [
                        new ToolAnalyticsField('hourly_rate', 'input', selector: '[name="hourly_rate"]'),
                        new ToolAnalyticsField('accrued_pto_hours', 'input', selector: '[name="accrued_pto_hours"]'),
                        new ToolAnalyticsField('planned_pto_hours', 'input', selector: '[name="planned_pto_hours"]'),
                        new ToolAnalyticsField('accrual_hours_per_pay_period', 'input', selector: '[name="accrual_hours_per_pay_period"]'),
                        new ToolAnalyticsField('pay_periods_before_leave', 'input', selector: '[name="pay_periods_before_leave"]'),
                        new ToolAnalyticsField('payout_percent', 'input', selector: '[name="payout_percent"]'),
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
            name: 'PTO Calculator',
            description: 'Estimate PTO value, planned usage, projected accrual, and payout using employer-policy assumptions.',
            category: ToolCategory::Labor,
            icon: 'bi-calendar2-check',
            routeName: 'tools.pto-calculator.index',
            vertical: 'contabilidade',
            version: '1.0.0',
            access: ToolAccess::Free,
            status: ToolStatus::Beta,
            position: 35,
            featured: false,
            supportsHistory: true,
            storesSensitiveData: false,
            keywords: ['PTO calculator', 'paid time off', 'vacation payout', 'PTO accrual'],
            capabilities: [ToolCapability::History, ToolCapability::VersionedPersistence, ToolCapability::Export],
            features: [
                new ToolFeature('vacation_calculation', 'PTO balance, planned usage, and estimated paid-leave value', ToolFeatureTier::Essential),
                new ToolFeature('accrual_projection', 'Projected PTO accrual before planned leave', ToolFeatureTier::Essential),
                new ToolFeature('policy_payout', 'User-entered PTO payout percentage for employer/state policy assumptions', ToolFeatureTier::Essential),
                new ToolFeature('calculation_memory', 'Detailed PTO calculation breakdown', ToolFeatureTier::Essential),
                new ToolFeature('history', 'Saved PTO calculation history and reuse', ToolFeatureTier::Plus),
                new ToolFeature('professional_export', 'Professional PDF/XLSX/CSV/JSON export', ToolFeatureTier::Plus),
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
            inputFields: ['hourly_rate', 'accrued_pto_hours', 'planned_pto_hours', 'accrual_hours_per_pay_period', 'pay_periods_before_leave', 'payout_percent'],
            resultFields: ['tool_slug', 'schema_version', 'summary', 'details', 'warnings', 'next_actions'],
            sensitiveFields: [],
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
        return 'tools-pto-calculator';
    }
}
