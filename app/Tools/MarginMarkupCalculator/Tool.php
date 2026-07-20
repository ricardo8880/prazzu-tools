<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator;

use App\Core\ToolIntegration\Data\ToolIntegrationManifest;
use App\Core\Tools\Contracts\HasMigrations;
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

final class Tool implements HasHistoryPolicy, HasMigrations, HasToolIntegrations, HasViews, HasWebRoutes, ToolModule
{
    public function integrations(): ToolIntegrationManifest
    {
        return new ToolIntegrationManifest(
            publishes: ['pricing-scenario:v1'],
            accepts: ['company-tax-snapshot:v1'],
        );
    }

    public function manifest(): ToolManifest
    {
        return new ToolManifest(
            slug: 'calculadora-margem-markup',
            name: 'Calculadora de Margem e Markup',
            description: 'Calcule preço de venda, lucro, margem e markup a partir dos custos do produto ou serviço.',
            category: ToolCategory::Calculators,
            icon: 'bi-percent',
            routeName: 'tools.calculadora-margem-markup.index',
            version: '1.0.0',
            access: ToolAccess::Free,
            status: ToolStatus::Active,
            position: 20,
            featured: true,
            supportsHistory: true,
            storesSensitiveData: false,
            keywords: ['margem', 'markup', 'preço de venda', 'lucro', 'custos'],
            capabilities: [
                ToolCapability::History,
                ToolCapability::VersionedPersistence,
                ToolCapability::Export,
                ToolCapability::PublishesIntegrations,
                ToolCapability::AcceptsIntegrations,
            ],
            features: [
                new ToolFeature('calculate', 'Cálculo individual completo', ToolFeatureTier::Essential),
                new ToolFeature('export', 'Exportação do cálculo', ToolFeatureTier::Plus),
                new ToolFeature('batch_processing', 'Cálculo e importação em lote', ToolFeatureTier::Plus),
                new ToolFeature('scenarios', 'Simulação de cenários', ToolFeatureTier::Plus),
                new ToolFeature('history', 'Histórico de cálculos', ToolFeatureTier::Plus),
            ],
            persistence: new ToolPersistencePolicy(enabled: true, schemaVersion: 1, retentionDays: 90, minimumReadableSchemaVersion: 1),
            export: new ToolExportPolicy(enabled: true, formats: ['csv', 'json', 'pdf', 'print']),
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
        return 'tools-calculadora-margem-markup';
    }

    public function historyPolicy(): ToolHistoryPolicy
    {
        return new ToolHistoryPolicy(
            enabled: true,
            retentionDays: 90,
            inputFields: [
                'calculation_type', 'reference_date', 'product_name', 'base_cost', 'additional_costs',
                'freight_cost', 'packaging_cost', 'fixed_expenses', 'desired_margin',
                'taxes_percentage', 'commission_percentage', 'card_fees_percentage',
                'marketplace_fees_percentage', 'products', 'scenarios',
            ],
            resultFields: [
                'calculation_type', 'total_cost', 'sale_price', 'gross_profit', 'net_profit',
                'taxes_amount', 'commission_amount', 'card_fees_amount', 'marketplace_fees_amount',
                'margin', 'markup', 'markup_multiplier', 'rule_version', 'results',
            ],
        );
    }

    public function migrationsPath(): string
    {
        return __DIR__.'/Infrastructure/Database/Migrations';
    }
}
