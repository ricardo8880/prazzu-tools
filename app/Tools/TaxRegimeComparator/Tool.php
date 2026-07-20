<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator;

use App\Core\ToolIntegration\Data\ToolIntegrationManifest;
use App\Core\Tools\Contracts\HasMigrations;
use App\Core\Tools\Contracts\HasServiceProviders;
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
use App\Tools\TaxRegimeComparator\Infrastructure\Providers\TaxRegimeComparatorServiceProvider;

final class Tool implements HasHistoryPolicy, HasMigrations, HasServiceProviders, HasToolIntegrations, HasViews, HasWebRoutes, ToolModule
{
    public const SLUG = 'comparador-tributario';

    public function integrations(): ToolIntegrationManifest
    {
        return new ToolIntegrationManifest(
            publishes: [],
            accepts: [],
        );
    }

    public function manifest(): ToolManifest
    {
        return new ToolManifest(
            slug: self::SLUG,
            name: 'Comparador Tributário',
            description: 'Compare estimativas do Simples Nacional, Lucro Presumido e Lucro Real com premissas e memória de cálculo transparentes.',
            category: ToolCategory::Fiscal,
            icon: 'bi-arrow-left-right',
            routeName: 'tools.comparador-tributario.index',
            version: '0.8.0',
            access: ToolAccess::Free,
            status: ToolStatus::Active,
            position: 20,
            featured: true,
            supportsHistory: true,
            storesSensitiveData: false,
            keywords: ['comparador tributário', 'regime tributário', 'simples nacional', 'lucro presumido', 'lucro real'],
            capabilities: [
                ToolCapability::History,
                ToolCapability::VersionedPersistence,
                ToolCapability::Export,
            ],
            features: [
                new ToolFeature('compare_tax_regimes', 'Comparação estimada entre regimes tributários', ToolFeatureTier::Essential),
                new ToolFeature('calculation_memory', 'Premissas e memória resumida da comparação', ToolFeatureTier::Essential),
                new ToolFeature('multiple_scenarios', 'Comparação de múltiplos cenários', ToolFeatureTier::Plus),
                new ToolFeature('annual_projection', 'Projeção tributária anual', ToolFeatureTier::Plus),
                new ToolFeature('professional_report', 'Relatório profissional da análise', ToolFeatureTier::Plus),
                new ToolFeature('history', 'Histórico de comparações', ToolFeatureTier::Plus),
                new ToolFeature('export', 'Exportação estruturada da comparação', ToolFeatureTier::Plus),
            ],
            persistence: new ToolPersistencePolicy(enabled: true, schemaVersion: 1, retentionDays: 365, minimumReadableSchemaVersion: 1),
            export: new ToolExportPolicy(enabled: true, formats: ['csv', 'json', 'pdf', 'print']),
            sharing: ToolSharingPolicy::disabled(),
            sensitiveData: ToolSensitiveDataPolicy::none(),
        );
    }

    public function historyPolicy(): ToolHistoryPolicy
    {
        return new ToolHistoryPolicy(
            enabled: true,
            retentionDays: 365,
            inputFields: [
                'reference_date', 'business_activity', 'monthly_revenue',
                'revenue_last_twelve_months', 'payroll_last_twelve_months',
                'monthly_operating_costs', 'monthly_deductible_expenses',
                'monthly_pis_cofins_credit_base', 'indirect_tax_rate',
                'state', 'municipality',
            ],
            resultFields: [
                'reference_date', 'winner', 'monthly_savings', 'annual_savings',
                'comparable_regime_count', 'rule_version', 'ranking', 'unavailable',
                'assumptions', 'warnings',
            ],
        );
    }

    public function migrationsPath(): string
    {
        return __DIR__.'/Infrastructure/Database/Migrations';
    }

    public function serviceProviders(): array
    {
        return [TaxRegimeComparatorServiceProvider::class];
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
        return 'tools-comparador-tributario';
    }
}
