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
use App\Core\Tools\History\Data\ToolHistoryPolicy;
use App\Core\Tools\Infrastructure\Data\ToolExportPolicy;
use App\Core\Tools\Infrastructure\Data\ToolPersistencePolicy;
use App\Core\Tools\Infrastructure\Data\ToolSensitiveDataPolicy;
use App\Core\Tools\Infrastructure\Data\ToolSharingPolicy;

final class Tool implements HasAnalyticsJourney, HasHistoryPolicy, HasToolIntegrations, HasViews, HasWebRoutes, ToolModule
{
    public const SLUG = 'calculadora-hora-extra';

    public function integrations(): ToolIntegrationManifest
    {
        return new ToolIntegrationManifest(publishes: [], accepts: []);
    }

    public function manifest(): ToolManifest
    {
        return new ToolManifest(slug: self::SLUG, name: 'Calculadora de Hora Extra, Adicional Noturno e DSR', description: 'Calcule hora extra, adicional noturno, DSR e projeções de reflexos com memória transparente.', category: ToolCategory::Labor, icon: 'bi-clock-history', vertical: 'contabilidade', routeName: 'tools.calculadora-hora-extra.index', version: '1.0.0', access: ToolAccess::Free, status: ToolStatus::Beta, position: 6, featured: true, supportsHistory: true, keywords: ['hora extra', 'adicional noturno', 'DSR', 'hora extra 50', 'hora extra 100', 'valor da hora'], capabilities: [ToolCapability::History, ToolCapability::VersionedPersistence, ToolCapability::Export], features: [new ToolFeature('calculate', 'Hora extra e valor da hora normal', ToolFeatureTier::Essential), new ToolFeature('memory', 'Memória de cálculo e fontes', ToolFeatureTier::Essential), new ToolFeature('night', 'Adicional noturno e hora reduzida', ToolFeatureTier::Plus), new ToolFeature('dsr', 'DSR parametrizado por calendário', ToolFeatureTier::Plus), new ToolFeature('reflexes', 'Projeção de 13º, férias + 1/3 e FGTS', ToolFeatureTier::Plus), new ToolFeature('export', 'CSV e impressão/PDF', ToolFeatureTier::Plus)], persistence: new ToolPersistencePolicy(true, 1, 365, 1), export: new ToolExportPolicy(true, ['csv', 'json', 'pdf']), sharing: ToolSharingPolicy::disabled(), sensitiveData: ToolSensitiveDataPolicy::none());
    }

    public function historyPolicy(): ToolHistoryPolicy
    {
        return new ToolHistoryPolicy(true, 365, ['competence', 'base_salary', 'monthly_hours', 'overtime_50_hours', 'overtime_100_hours', 'custom_overtime_hours', 'custom_premium', 'night_clock_hours', 'night_overtime_hours', 'night_overtime_premium', 'working_days', 'rest_days', 'include_dsr', 'include_reflexes'], ['hourly', 'overtime', 'night', 'dsr', 'total', 'summary', 'details', 'warnings', 'calculation_memory'], []);
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
        return 'tools-calculadora-hora-extra';
    }

    public function analyticsJourney(): ToolAnalyticsJourney
    {
        return new ToolAnalyticsJourney(toolSlug: 'calculadora-hora-extra', forms: [new ToolAnalyticsForm(key: 'main', steps: ['input'], fields: [new ToolAnalyticsField('salary', 'input', selector: '[name="salary"]'), new ToolAnalyticsField('monthly_hours', 'input', selector: '[name="monthly_hours"]'), new ToolAnalyticsField('overtime_50_hours', 'input', selector: '[name="overtime_50_hours"]'), new ToolAnalyticsField('overtime_100_hours', 'input', selector: '[name="overtime_100_hours"]'), new ToolAnalyticsField('night_hours', 'input', selector: '[name="night_hours"]'), new ToolAnalyticsField('night_additional_rate', 'input', selector: '[name="night_additional_rate"]'), new ToolAnalyticsField('night_reduction', 'input', selector: '[name="night_reduction"]'), new ToolAnalyticsField('working_days', 'input', selector: '[name="working_days"]'), new ToolAnalyticsField('sundays_holidays', 'input', selector: '[name="sundays_holidays"]'), new ToolAnalyticsField('include_dsr', 'input', selector: '[name="include_dsr"]'), new ToolAnalyticsField('project_reflexes', 'input', selector: '[name="project_reflexes"]'), new ToolAnalyticsField('vacation_rate', 'input', selector: '[name="vacation_rate"]'), new ToolAnalyticsField('thirteenth_rate', 'input', selector: '[name="thirteenth_rate"]'), new ToolAnalyticsField('fgts_rate', 'input', selector: '[name="fgts_rate"]'), new ToolAnalyticsField('competence', 'input', selector: '[name="competence"]')], actions: ['calculate', 'export', 'share'], selector: 'form[action*="calculate"]', resultSelector: '[data-analytics-result="main"]')]);
    }
}
