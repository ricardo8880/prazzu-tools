<?php

declare(strict_types=1);

namespace App\Tools\OvertimeCalculator;

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
    public const SLUG = 'overtime-pay-calculator';

    public function integrations(): ToolIntegrationManifest
    {
        return new ToolIntegrationManifest(publishes: [], accepts: []);
    }

    public function manifest(): ToolManifest
    {
        return new ToolManifest(slug: self::SLUG, name: 'Overtime Pay Calculator', description: 'Estimate weekly overtime pay for a covered nonexempt hourly employee under the federal FLSA.', category: ToolCategory::Labor, icon: 'bi-clock-history', vertical: 'contabilidade', routeName: 'tools.overtime-pay-calculator.index', version: '1.0.0', access: ToolAccess::Free, status: ToolStatus::Active, position: 6, featured: true, supportsHistory: true, keywords: ['overtime calculator', 'FLSA overtime', 'time and a half', 'regular rate'], capabilities: [ToolCapability::History, ToolCapability::VersionedPersistence, ToolCapability::Export], features: [new ToolFeature('calculate', 'Regular and overtime pay', ToolFeatureTier::Essential), new ToolFeature('memory', 'Calculation details and sources', ToolFeatureTier::Essential), new ToolFeature('night', 'Custom premium scenarios', ToolFeatureTier::Plus), new ToolFeature('dsr', 'Calendar-aware workweek scenarios', ToolFeatureTier::Plus), new ToolFeature('reflexes', 'Additional compensation projections', ToolFeatureTier::Plus), new ToolFeature('export', 'CSV and print/PDF export', ToolFeatureTier::Plus)], persistence: new ToolPersistencePolicy(true, 1, 365, 1), export: new ToolExportPolicy(true, ['csv', 'json', 'pdf']), sharing: ToolSharingPolicy::disabled(), sensitiveData: ToolSensitiveDataPolicy::none());
    }

    public function historyPolicy(): ToolHistoryPolicy
    {
        return new ToolHistoryPolicy(true, 365, ['competence', 'base_salary', 'monthly_hours', 'overtime_50_hours', 'overtime_100_hours', 'custom_overtime_hours', 'custom_premium', 'night_clock_hours', 'night_overtime_hours', 'night_overtime_premium', 'working_days', 'rest_days', 'include_dsr', 'include_reflexes'], ['hourly', 'overtime', 'night', 'dsr', 'total', 'summary', 'details', 'warnings', 'calculation_memory'], []);
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
        return 'tools-overtime-pay-calculator';
    }

    public function analyticsJourney(): ToolAnalyticsJourney
    {
        return new ToolAnalyticsJourney(toolSlug: 'overtime-pay-calculator', forms: [new ToolAnalyticsForm(key: 'main', steps: ['input'], fields: [new ToolAnalyticsField('salary', 'input', selector: '[name="salary"]'), new ToolAnalyticsField('monthly_hours', 'input', selector: '[name="monthly_hours"]'), new ToolAnalyticsField('overtime_50_hours', 'input', selector: '[name="overtime_50_hours"]'), new ToolAnalyticsField('overtime_100_hours', 'input', selector: '[name="overtime_100_hours"]'), new ToolAnalyticsField('night_hours', 'input', selector: '[name="night_hours"]'), new ToolAnalyticsField('night_additional_rate', 'input', selector: '[name="night_additional_rate"]'), new ToolAnalyticsField('night_reduction', 'input', selector: '[name="night_reduction"]'), new ToolAnalyticsField('working_days', 'input', selector: '[name="working_days"]'), new ToolAnalyticsField('sundays_holidays', 'input', selector: '[name="sundays_holidays"]'), new ToolAnalyticsField('include_dsr', 'input', selector: '[name="include_dsr"]'), new ToolAnalyticsField('project_reflexes', 'input', selector: '[name="project_reflexes"]'), new ToolAnalyticsField('vacation_rate', 'input', selector: '[name="vacation_rate"]'), new ToolAnalyticsField('thirteenth_rate', 'input', selector: '[name="thirteenth_rate"]'), new ToolAnalyticsField('fgts_rate', 'input', selector: '[name="fgts_rate"]'), new ToolAnalyticsField('competence', 'input', selector: '[name="competence"]')], actions: ['calculate', 'export', 'share'], selector: 'form[action*="calculate"]', resultSelector: '[data-analytics-result="main"]')]);
    }
}
