<?php

declare(strict_types=1);

namespace App\Tools\CfopAdvisor;

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

final class Tool implements HasAnalyticsJourney, HasToolIntegrations, HasWebRoutes, HasViews, ToolModule
{
    public const SLUG = 'consultor-validador-cfop';
    public function integrations(): ToolIntegrationManifest { return new ToolIntegrationManifest(publishes: [], accepts: []); }
    public function manifest(): ToolManifest
    {
        return new ToolManifest(slug: self::SLUG, name: 'Consultor e Validador de CFOP', description: 'Valide a estrutura do CFOP, entenda direção e abrangência da operação e consulte explicações de códigos fiscais recorrentes com referência oficial do CONFAZ.', category: ToolCategory::Fiscal, icon: 'bi-search', routeName: 'tools.consultor-validador-cfop.index', vertical: 'contabilidade', version: '1.0.0', access: ToolAccess::Free, status: ToolStatus::Beta, position: 210, featured: true, supportsHistory: false, storesSensitiveData: false, keywords: ['CFOP', 'consulta CFOP', 'validador CFOP', 'CONFAZ', 'entrada', 'saída'], capabilities: [ToolCapability::Export], features: [new ToolFeature('analyze', 'Análise principal completa e explicada', ToolFeatureTier::Essential), new ToolFeature('catalog_details', 'Detalhes do catálogo e descrição oficial quando disponível', ToolFeatureTier::Plus)], persistence: ToolPersistencePolicy::disabled(), export: new ToolExportPolicy(true, ['pdf', 'xlsx']), sharing: ToolSharingPolicy::disabled(), sensitiveData: ToolSensitiveDataPolicy::none());
    }
    public function analyticsJourney(): ToolAnalyticsJourney
    {
        return new ToolAnalyticsJourney(toolSlug: self::SLUG, forms: [new ToolAnalyticsForm(key: 'main', steps: ['input'], fields: [new ToolAnalyticsField('cfop', 'input', selector: '[name="cfop"]')], actions: ['analyze'], selector: 'form[action*="consultor-validador-cfop"]', resultSelector: '[data-testid="tool-result"]')]);
    }
    public function webRoutesPath(): string { return __DIR__.'/Routes/web.php'; }
    public function viewsPath(): string { return __DIR__.'/Resources/views'; }
    public function viewsNamespace(): string { return 'tools-consultor-validador-cfop'; }
}
