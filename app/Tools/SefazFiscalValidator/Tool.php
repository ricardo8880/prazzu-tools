<?php

declare(strict_types=1);

namespace App\Tools\SefazFiscalValidator;

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
    public const SLUG = 'validador-fiscal-sefaz';
    public function integrations(): ToolIntegrationManifest { return new ToolIntegrationManifest(publishes: [], accepts: []); }
    public function manifest(): ToolManifest
    {
        return new ToolManifest(slug: self::SLUG, name: 'Validador Fiscal SEFAZ', description: 'Faça diagnóstico offline de chave de acesso NF-e/NFC-e, identificando estrutura, UF, competência, modelo, emissão, ambiente e dígito verificador quando aplicável.', category: ToolCategory::Fiscal, icon: 'bi-file-earmark-check', routeName: 'tools.validador-fiscal-sefaz.index', vertical: 'contabilidade', version: '1.0.0', access: ToolAccess::Free, status: ToolStatus::Beta, position: 210, featured: true, supportsHistory: false, storesSensitiveData: false, keywords: ['SEFAZ', 'NF-e', 'NFC-e', 'chave de acesso', 'modelo 55', 'modelo 65', 'validação fiscal'], capabilities: [ToolCapability::Export], features: [new ToolFeature('analyze', 'Análise principal completa e explicada', ToolFeatureTier::Essential), new ToolFeature('key_breakdown', 'Decodificação detalhada dos campos da chave', ToolFeatureTier::Plus)], persistence: ToolPersistencePolicy::disabled(), export: new ToolExportPolicy(true, ['pdf', 'xlsx']), sharing: ToolSharingPolicy::disabled(), sensitiveData: ToolSensitiveDataPolicy::none());
    }
    public function analyticsJourney(): ToolAnalyticsJourney
    {
        return new ToolAnalyticsJourney(toolSlug: self::SLUG, forms: [new ToolAnalyticsForm(key: 'main', steps: ['input'], fields: [new ToolAnalyticsField('access_key', 'input', selector: '[name="access_key"]')], actions: ['analyze'], selector: 'form[action*="validador-fiscal-sefaz"]', resultSelector: '[data-testid="tool-result"]')]);
    }
    public function webRoutesPath(): string { return __DIR__.'/Routes/web.php'; }
    public function viewsPath(): string { return __DIR__.'/Resources/views'; }
    public function viewsNamespace(): string { return 'tools-validador-fiscal-sefaz'; }
}
