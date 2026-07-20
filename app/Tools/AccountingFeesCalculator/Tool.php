<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator;

use App\Core\Tools\Contracts\HasMigrations;
use App\Core\Tools\Contracts\HasViews;
use App\Core\Tools\Contracts\HasWebRoutes;
use App\Core\Tools\Contracts\HasToolIntegrations;
use App\Core\Tools\Contracts\ToolModule;
use App\Core\ToolIntegration\Data\ToolIntegrationManifest;
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
use App\Core\Tools\Infrastructure\Enums\SensitiveDataMode;

final class Tool implements HasToolIntegrations, HasHistoryPolicy, HasMigrations, HasViews, HasWebRoutes, ToolModule
{
    public function integrations(): ToolIntegrationManifest
    {
        return new ToolIntegrationManifest(
            publishes: ['company-operating-profile:v1'],
            accepts: ['company-tax-snapshot:v1'],
        );
    }

    public function manifest(): ToolManifest
    {
        return new ToolManifest(
            slug: 'calculadora-de-honorarios-contabeis',
            name: 'Calculadora de Honorários Contábeis',
            description: 'Estime honorários contábeis com base no porte, regime tributário e complexidade da operação.',
            category: ToolCategory::Calculators,
            icon: 'bi-calculator',
            routeName: 'tools.calculadora-de-honorarios-contabeis.index',
            version: '1.2.0',
            access: ToolAccess::Free,
            status: ToolStatus::Active,
            position: 30,
            featured: true,
            supportsHistory: true,
            storesSensitiveData: true,
            keywords: ['honorários contábeis', 'precificação contábil', 'contador', 'mensalidade contábil', 'proposta comercial', 'contrato contábil'],
            capabilities: [
                ToolCapability::History,
                ToolCapability::VersionedPersistence,
                ToolCapability::Export,
                ToolCapability::SensitiveData,
                ToolCapability::PublishesIntegrations,
                ToolCapability::AcceptsIntegrations,
            ],
            features: [
                new ToolFeature('calculate', 'Precificação completa de honorários', ToolFeatureTier::Essential),
                new ToolFeature('adjust_fee', 'Cálculo completo de reajuste', ToolFeatureTier::Essential),
                new ToolFeature('commercial_proposal', 'Geração de proposta comercial', ToolFeatureTier::Plus),
                new ToolFeature('service_contract', 'Geração de contrato de serviços', ToolFeatureTier::Plus),
                new ToolFeature('history', 'Histórico, favoritos e duplicação', ToolFeatureTier::Plus),
                new ToolFeature('history_export', 'Exportação do histórico', ToolFeatureTier::Plus),
            ],
            persistence: new ToolPersistencePolicy(enabled: true, schemaVersion: 1, retentionDays: 365, minimumReadableSchemaVersion: 1),
            export: new ToolExportPolicy(enabled: true, formats: ['csv', 'json', 'pdf', 'print']),
            sharing: ToolSharingPolicy::disabled(),
            sensitiveData: new ToolSensitiveDataPolicy(SensitiveDataMode::Encrypted, ['history_payload']),
        );
    }

    public function historyPolicy(): ToolHistoryPolicy
    {
        return new ToolHistoryPolicy(
            enabled: true,
            retentionDays: 365,
            inputFields: [
                'monthly_revenue', 'employees', 'partners', 'monthly_invoices',
                'monthly_bank_transactions', 'tax_regime', 'business_segment',
                'complexity', 'scenario_label', 'index_type', 'reference_period',
                'current_value', 'percentage', 'notes',
            ],
            resultFields: [
                'minimum_fee', 'recommended_fee', 'upper_reference_fee',
                'complexity_score', 'complexity_level', 'rule_version',
                'current_value_cents', 'difference_cents', 'adjusted_value_cents',
            ],
            sensitiveFields: ['scenario_label', 'notes'],
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
        return 'tools-calculadora-de-honorarios-contabeis';
    }

    public function migrationsPath(): string
    {
        return __DIR__.'/Infrastructure/Database/Migrations';
    }
}
