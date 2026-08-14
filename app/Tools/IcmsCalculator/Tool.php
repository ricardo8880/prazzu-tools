<?php

declare(strict_types=1);

namespace App\Tools\IcmsCalculator;

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
    public const SLUG = 'calculadora-icms-proprio';
    public function integrations(): ToolIntegrationManifest { return new ToolIntegrationManifest(publishes: [], accepts: []); }
    public function manifest(): ToolManifest
    {
        return new ToolManifest(slug: self::SLUG, name: 'Calculadora de ICMS Próprio', description: 'Calcule ICMS próprio com base e alíquota informadas, redução de base e opção de cálculo por dentro quando o valor informado está sem ICMS.', category: ToolCategory::Fiscal, icon: 'bi-calculator', routeName: 'tools.calculadora-icms-proprio.index', vertical: 'contabilidade', version: '1.0.0', access: ToolAccess::Free, status: ToolStatus::Beta, position: 210, featured: true, supportsHistory: false, storesSensitiveData: false, keywords: ['ICMS', 'ICMS próprio', 'base de cálculo', 'alíquota', 'redução de base', 'por dentro'], capabilities: [ToolCapability::Export], features: [new ToolFeature('analyze', 'Análise principal completa e explicada', ToolFeatureTier::Essential), new ToolFeature('inside_calculation', 'Cálculo por dentro quando o valor informado está sem ICMS', ToolFeatureTier::Plus)], persistence: ToolPersistencePolicy::disabled(), export: new ToolExportPolicy(true, ['pdf', 'xlsx']), sharing: ToolSharingPolicy::disabled(), sensitiveData: ToolSensitiveDataPolicy::none());
    }
    public function analyticsJourney(): ToolAnalyticsJourney
    {
        return new ToolAnalyticsJourney(toolSlug: self::SLUG, forms: [new ToolAnalyticsForm(key: 'main', steps: ['input'], fields: [new ToolAnalyticsField('value', 'input', selector: '[name="value"]')], actions: ['analyze'], selector: 'form[action*="calculadora-icms-proprio"]', resultSelector: '[data-testid="tool-result"]')]);
    }
    public function webRoutesPath(): string { return __DIR__.'/Routes/web.php'; }
    public function viewsPath(): string { return __DIR__.'/Resources/views'; }
    public function viewsNamespace(): string { return 'tools-calculadora-icms-proprio'; }
}
