<?php

declare(strict_types=1);

namespace App\Tools\AssetDepreciationCalculator;

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
    public const SLUG = 'calculadora-depreciacao-ativos';

    public function integrations(): ToolIntegrationManifest
    {
        return new ToolIntegrationManifest(publishes: [], accepts: []);
    }

    public function manifest(): ToolManifest
    {
        return new ToolManifest(
            slug: self::SLUG,
            name: 'Calculadora de Depreciação de Ativos',
            description: 'Calcule depreciação mensal e anual, acompanhe o valor contábil e projete a evolução de um ou vários ativos.',
            category: ToolCategory::Calculators,
            icon: 'bi-building-down',
            routeName: 'tools.calculadora-depreciacao-ativos.index',
            vertical: 'contabilidade',
            version: '1.1.0',
            access: ToolAccess::Free,
            status: ToolStatus::Beta,
            position: 195,
            featured: true,
            supportsHistory: false,
            storesSensitiveData: false,
            keywords: ['depreciação', 'ativo imobilizado', 'vida útil', 'valor contábil', 'patrimônio', 'imobilizado'],
            capabilities: [ToolCapability::Export],
            features: [
                new ToolFeature('calculate', 'Depreciação mensal/anual e valor contábil de um ativo', ToolFeatureTier::Essential),
                new ToolFeature('memory', 'Memória de cálculo e projeção do ativo', ToolFeatureTier::Essential),
                new ToolFeature('multiple_assets', 'Cadastro e uso de vários ativos', ToolFeatureTier::Plus),
                new ToolFeature('methods', 'Métodos linear, saldos decrescentes e soma dos dígitos', ToolFeatureTier::Plus),
                new ToolFeature('portfolio_projection', 'Projeção patrimonial consolidada', ToolFeatureTier::Plus),
                new ToolFeature('export', 'Exportação em PDF e XLSX', ToolFeatureTier::Plus),
            ],
            persistence: ToolPersistencePolicy::disabled(),
            export: new ToolExportPolicy(enabled: true, formats: ['pdf', 'xlsx']),
            sharing: ToolSharingPolicy::disabled(),
            sensitiveData: ToolSensitiveDataPolicy::none(),
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
        return 'tools-calculadora-depreciacao-ativos';
    }

    public function analyticsJourney(): ToolAnalyticsJourney
    {
        return new ToolAnalyticsJourney(
            toolSlug: self::SLUG,
            forms: [
                new ToolAnalyticsForm(
                    key: 'main',
                    steps: ['asset', 'plus'],
                    fields: [
                        new ToolAnalyticsField('asset_name', 'asset', selector: '[name="asset_name"]'),
                        new ToolAnalyticsField('asset_value', 'asset', selector: '[name="asset_value"]'),
                        new ToolAnalyticsField('residual_value', 'asset', selector: '[name="residual_value"]'),
                        new ToolAnalyticsField('useful_life_years', 'asset', selector: '[name="useful_life_years"]'),
                        new ToolAnalyticsField('method', 'plus', selector: '[name="method"]'),
                    ],
                    actions: ['calculate', 'export'],
                    selector: 'form[action*="calculadora-depreciacao-ativos"]',
                    resultSelector: '[data-analytics-result="main"]',
                ),
            ],
        );
    }
}
