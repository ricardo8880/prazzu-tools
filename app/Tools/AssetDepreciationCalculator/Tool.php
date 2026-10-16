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
    public const SLUG = 'asset-depreciation-calculator';

    public function integrations(): ToolIntegrationManifest
    {
        return new ToolIntegrationManifest(publishes: [], accepts: []);
    }

    public function manifest(): ToolManifest
    {
        return new ToolManifest(
            slug: self::SLUG,
            name: 'Asset Depreciation Calculator',
            description: 'Estimate straight-line or declining-balance depreciation for business assets in U.S. dollars.',
            category: ToolCategory::Calculators,
            icon: 'bi-building-down',
            routeName: 'tools.asset-depreciation-calculator.index',
            vertical: 'contabilidade',
            version: '1.1.0',
            access: ToolAccess::Free,
            status: ToolStatus::Beta,
            position: 195,
            featured: true,
            supportsHistory: false,
            storesSensitiveData: false,
            keywords: ['asset depreciation', 'depreciation calculator', 'straight line depreciation', 'declining balance'],
            capabilities: [ToolCapability::Export],
            features: [
                new ToolFeature('calculate', 'Monthly/annual depreciation and asset book value', ToolFeatureTier::Essential),
                new ToolFeature('memory', 'Calculation details and asset projection', ToolFeatureTier::Essential),
                new ToolFeature('multiple_assets', 'Save and reuse multiple assets', ToolFeatureTier::Plus),
                new ToolFeature('methods', 'Straight-line, declining-balance, and sum-of-the-years-digits methods', ToolFeatureTier::Plus),
                new ToolFeature('portfolio_projection', 'Consolidated asset projection', ToolFeatureTier::Plus),
                new ToolFeature('export', 'PDF and XLSX export', ToolFeatureTier::Plus),
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
        return 'tools-asset-depreciation-calculator';
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
                    selector: 'form[action*="asset-depreciation-calculator"]',
                    resultSelector: '[data-analytics-result="main"]',
                ),
            ],
        );
    }
}
