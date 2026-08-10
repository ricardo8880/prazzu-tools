<?php

declare(strict_types=1);

namespace App\Tools\WorkingCapitalCalculator;

use App\Core\ToolIntegration\Data\ToolIntegrationManifest;
use App\Core\Tools\Contracts\HasToolIntegrations;
use App\Core\Tools\Contracts\HasViews;
use App\Core\Tools\Contracts\HasWebRoutes;
use App\Core\Tools\Contracts\ToolModule;
use App\Core\Tools\Analytics\Contracts\HasAnalyticsJourney;
use App\Core\Tools\Analytics\Data\ToolAnalyticsField;
use App\Core\Tools\Analytics\Data\ToolAnalyticsForm;
use App\Core\Tools\Analytics\Data\ToolAnalyticsJourney;
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
    public function integrations(): ToolIntegrationManifest
    {
        return new ToolIntegrationManifest(publishes: [], accepts: []);
    }

    public function analyticsJourney(): ToolAnalyticsJourney
    {
        return new ToolAnalyticsJourney(
            toolSlug: 'capital-de-giro',
            forms: [
                new ToolAnalyticsForm(
                    key: 'main',
                    steps: ['assets', 'liabilities'],
                    fields: [
                    new ToolAnalyticsField('cash', 'assets', selector: '[name="cash"]'),
                    new ToolAnalyticsField('receivables', 'assets', selector: '[name="receivables"]'),
                    new ToolAnalyticsField('inventory', 'assets', selector: '[name="inventory"]'),
                    new ToolAnalyticsField('other_current_assets', 'assets', selector: '[name="other_current_assets"]'),
                    new ToolAnalyticsField('suppliers', 'liabilities', selector: '[name="suppliers"]'),
                    new ToolAnalyticsField('other_operating_liabilities', 'liabilities', selector: '[name="other_operating_liabilities"]'),
                    new ToolAnalyticsField('loans', 'liabilities', selector: '[name="loans"]'),
                    new ToolAnalyticsField('other_current_liabilities', 'liabilities', selector: '[name="other_current_liabilities"]'),
                    ],
                    actions: ['calculate'],
                    selector: '[data-analytics-form="main"]',
                    resultSelector: '[data-analytics-result="main"]',
                ),
            ],
        );
    }

    public function manifest(): ToolManifest
    {
        return new ToolManifest(
            slug: 'capital-de-giro',
            name: 'Calculadora de Capital de Giro',
            description: 'Calcule a necessidade operacional, o capital circulante líquido e a necessidade adicional de recursos.',
            category: ToolCategory::Calculators,
            icon: 'bi-cash-stack',
            routeName: 'tools.capital-de-giro.index',
            vertical: 'contabilidade',
            version: '1.0.0',
            access: ToolAccess::Free,
            status: ToolStatus::Beta,
            position: 170,
            supportsHistory: true,
            storesSensitiveData: false,
            keywords: ['capital de giro', 'ncg', 'ccl', 'liquidez', 'caixa'],
            capabilities: [ToolCapability::History, ToolCapability::VersionedPersistence, ToolCapability::Export],
            features: [
                new ToolFeature('calculate', 'Cálculo completo com memória', ToolFeatureTier::Essential),
                new ToolFeature('projections', 'Projeções, cenários e histórico financeiro', ToolFeatureTier::Plus),
            ],
            persistence: new ToolPersistencePolicy(enabled: true, schemaVersion: 1, retentionDays: 365, minimumReadableSchemaVersion: 1),
            export: new ToolExportPolicy(enabled: true, formats: ['csv', 'json', 'pdf']),
            sharing: ToolSharingPolicy::disabled(),
            sensitiveData: ToolSensitiveDataPolicy::none(),
        );
    }

    public function historyPolicy(): ToolHistoryPolicy
    {
        return new ToolHistoryPolicy(
            enabled: true,
            retentionDays: 365,
            inputFields: ['cash', 'receivables', 'inventory', 'other_current_assets', 'suppliers', 'other_operating_liabilities', 'loans', 'other_current_liabilities'],
            resultFields: ['required_capital', 'operating_need', 'net_working_capital', 'funding_gap'],
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
        return 'tools-capital-de-giro';
    }
}
