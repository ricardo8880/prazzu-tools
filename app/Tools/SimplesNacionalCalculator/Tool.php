<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator;

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
    public const SLUG = 'calculadora-simples-nacional';

    public const HISTORY_RULE_VERSION = '1.0.0';

    public function integrations(): ToolIntegrationManifest
    {
        return new ToolIntegrationManifest(
            publishes: ['company-tax-snapshot:v1'],
            accepts: ['company-operating-profile:v1'],
        );
    }

    public function manifest(): ToolManifest
    {
        return new ToolManifest(
            slug: self::SLUG,
            name: 'Calculadora de Simples Nacional',
            description: 'Estime o DAS, identifique faixa, anexo e alíquota efetiva, incluindo análise do Fator R.',
            category: ToolCategory::Fiscal,
            icon: 'bi-calculator',
            routeName: 'tools.calculadora-simples-nacional.index',
            version: '1.2.0',
            access: ToolAccess::Free,
            status: ToolStatus::Beta,
            position: 10,
            featured: true,
            supportsHistory: true,
            storesSensitiveData: true,
            keywords: ['simples nacional', 'das', 'fator r', 'anexo', 'alíquota efetiva'],
            capabilities: [
                ToolCapability::History,
                ToolCapability::VersionedPersistence,
                ToolCapability::Export,
                ToolCapability::SensitiveData,
                ToolCapability::PublishesIntegrations,
                ToolCapability::AcceptsIntegrations,
            ],
            features: [
                new ToolFeature('calculate', 'Cálculo por anexo e faixa', ToolFeatureTier::Essential),
                new ToolFeature('factor_r', 'Cálculo do Fator R', ToolFeatureTier::Essential),
                new ToolFeature('effective_rate', 'Alíquota efetiva', ToolFeatureTier::Essential),
                new ToolFeature('estimated_das', 'DAS estimado', ToolFeatureTier::Essential),
                new ToolFeature('compare_scenarios', 'Comparação de cenários', ToolFeatureTier::Plus),
                new ToolFeature('compare_annexes', 'Comparação entre anexos', ToolFeatureTier::Plus),
                new ToolFeature('annual_projection', 'Projeção anual', ToolFeatureTier::Plus),
                new ToolFeature('alerts', 'Alertas tributários', ToolFeatureTier::Plus),
                new ToolFeature('monthly_history', 'Histórico mensal', ToolFeatureTier::Plus),
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
            inputFields: ['reference_month', 'annex', 'rbt12', 'monthly_revenue'],
            resultFields: [
                'annex', 'annex_label', 'rbt12', 'monthly_revenue', 'bracket',
                'bracket_from', 'bracket_until', 'nominal_rate', 'deduction',
                'effective_rate', 'estimated_das', 'formula', 'rule_version',
                'rule_valid_from',
            ],
            sensitiveFields: ['rbt12', 'monthly_revenue'],
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
        return 'tools-calculadora-simples-nacional';
    }

    public function migrationsPath(): string
    {
        return __DIR__.'/Infrastructure/Database/Migrations';
    }
}
