<?php

declare(strict_types=1);

namespace App\Tools\ProfitDistributionBalanceSimulator;

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
use App\Core\Tools\Infrastructure\Data\ToolExportPolicy;
use App\Core\Tools\Infrastructure\Data\ToolPersistencePolicy;
use App\Core\Tools\Infrastructure\Data\ToolSensitiveDataPolicy;
use App\Core\Tools\Infrastructure\Data\ToolSharingPolicy;

final class Tool implements HasAnalyticsJourney, HasToolIntegrations, HasViews, HasWebRoutes, ToolModule
{
    public const SLUG = 'simulador-distribuicao-lucros-balanco';

    public function integrations(): ToolIntegrationManifest
    {
        return new ToolIntegrationManifest(publishes: [], accepts: []);
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
        return 'tools-simulador-distribuicao-lucros-balanco';
    }

    public function manifest(): ToolManifest
    {
        return new ToolManifest(slug: self::SLUG, name: 'Simulador de Distribuição de Lucros com Balanço × sem Balanço', description: 'Compare a capacidade estimada de distribuição usando lucro contábil e parâmetros informados para um cenário sem balanço.', category: ToolCategory::Fiscal, icon: 'bi-diagram-3', routeName: 'tools.simulador-distribuicao-lucros-balanco.index', vertical: 'contabilidade', version: '1.0.0', access: ToolAccess::Free, status: ToolStatus::Beta, position: 204, featured: true, supportsHistory: false, storesSensitiveData: false, keywords: ['distribuição de lucros', 'balanço', 'sem balanço', 'escrituração', 'pró-labore'], capabilities: [ToolCapability::Export], features: [new ToolFeature('comparison', 'Comparação com balanço × sem balanço por parâmetros informados', ToolFeatureTier::Essential), new ToolFeature('memory', 'Memória dos dois limites estimados', ToolFeatureTier::Essential), new ToolFeature('planning', 'Simulação de escrituração/balanço, pró-labore, tributos, distribuição acumulada e planejamento anual', ToolFeatureTier::Plus), new ToolFeature('report', 'Relatório PDF/XLSX', ToolFeatureTier::Plus)], persistence: ToolPersistencePolicy::disabled(), export: new ToolExportPolicy(enabled: true, formats: ['pdf', 'xlsx']), sharing: ToolSharingPolicy::disabled(), sensitiveData: ToolSensitiveDataPolicy::none());
    }

    public function analyticsJourney(): ToolAnalyticsJourney
    {
        return new ToolAnalyticsJourney(toolSlug: self::SLUG, forms: [new ToolAnalyticsForm(key: 'main', steps: ['comparison', 'planning'], fields: [new ToolAnalyticsField('annual_revenue', 'comparison', selector: '[name="annual_revenue"]'), new ToolAnalyticsField('accounting_profit', 'comparison', selector: '[name="accounting_profit"]'), new ToolAnalyticsField('reference_margin', 'comparison', selector: '[name="reference_margin"]'), new ToolAnalyticsField('taxes_on_revenue', 'comparison', selector: '[name="taxes_on_revenue"]'), new ToolAnalyticsField('monthly_pro_labore','planning',selector: '[name="monthly_pro_labore"]')], actions: ['calculate', 'export'], selector: 'form[action*="simulador-distribuicao-lucros-balanco"]', resultSelector: '[data-analytics-result="main"]')]);
    }
}
