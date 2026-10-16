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
        return new ToolManifest(slug: self::SLUG, name: 'Overtime Pay Calculator', description: 'Estimate weekly overtime pay for a covered nonexempt hourly employee under the federal FLSA.', category: ToolCategory::Labor, icon: 'bi-clock-history', vertical: 'contabilidade', routeName: 'tools.overtime-pay-calculator.index', version: '1.0.0', access: ToolAccess::Free, status: ToolStatus::Active, position: 6, featured: true, supportsHistory: true, keywords: ['overtime calculator', 'FLSA overtime', 'time and a half', 'regular rate'], capabilities: [ToolCapability::History, ToolCapability::VersionedPersistence, ToolCapability::Export], features: [new ToolFeature('calculate', 'Regular and overtime pay', ToolFeatureTier::Essential), new ToolFeature('memory', 'Calculation details and sources', ToolFeatureTier::Essential), new ToolFeature('export', 'CSV and print/PDF export', ToolFeatureTier::Plus)], persistence: new ToolPersistencePolicy(true, 1, 365, 1), export: new ToolExportPolicy(true, ['csv', 'json', 'pdf']), sharing: ToolSharingPolicy::disabled(), sensitiveData: ToolSensitiveDataPolicy::none());
    }

    public function historyPolicy(): ToolHistoryPolicy
    {
        return new ToolHistoryPolicy(true, 365, ['competence', 'hourly_rate', 'regular_hours', 'overtime_hours', 'overtime_multiplier', 'nondiscretionary_bonus'], ['summary', 'details', 'warnings', 'calculation_memory'], []);
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
        return new ToolAnalyticsJourney(toolSlug: 'overtime-pay-calculator', forms: [new ToolAnalyticsForm(key: 'main', steps: ['input'], fields: [new ToolAnalyticsField('competence', 'input', selector: '[name="competence"]'), new ToolAnalyticsField('hourly_rate', 'input', selector: '[name="hourly_rate"]'), new ToolAnalyticsField('regular_hours', 'input', selector: '[name="regular_hours"]'), new ToolAnalyticsField('overtime_hours', 'input', selector: '[name="overtime_hours"]'), new ToolAnalyticsField('overtime_multiplier', 'input', selector: '[name="overtime_multiplier"]'), new ToolAnalyticsField('nondiscretionary_bonus', 'input', selector: '[name="nondiscretionary_bonus"]')], actions: ['calculate', 'export'], selector: 'form[action*="calculate"]', resultSelector: '[data-analytics-result="main"]')]);
    }
}
