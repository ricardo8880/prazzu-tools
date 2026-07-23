<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreProfitDistributionCalculator;

use App\Core\Tools\Api\Contracts\HasApiActions;
use App\Core\ToolIntegration\Data\ToolIntegrationManifest;
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

use App\Tools\ProLaboreProfitDistributionCalculator\Api\Actions\CalculateApiAction;

final class Tool implements HasApiActions, HasHistoryPolicy, HasToolIntegrations, HasViews, HasWebRoutes, ToolModule
{
    public function apiActions(): array
    {
        return [CalculateApiAction::class];
    }

    public const SLUG = 'calculadora-pro-labore-distribuicao-lucros';

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
            name: 'Calculadora de Pró-Labore e Distribuição de Lucros',
            description: 'Simule pró-labore, INSS, IRRF e distribuição de lucros para um ou vários sócios e competências, com memória de cálculo transparente.',
            category: ToolCategory::Fiscal,
            icon: 'bi-cash-coin',
            routeName: 'tools.calculadora-pro-labore-distribuicao-lucros.index',
            version: '1.0.0-rc.1',
            access: ToolAccess::Free,
            status: ToolStatus::Beta,
            position: 30,
            featured: true,
            supportsHistory: true,
            storesSensitiveData: false,
            keywords: [
                'pró-labore',
                'pro labore',
                'distribuição de lucros',
                'lucros e dividendos',
                'retirada de sócios',
                'inss pró-labore',
                'irrf pró-labore',
            ],
            capabilities: [
                ToolCapability::History,
                ToolCapability::VersionedPersistence,
                ToolCapability::Export,
            ],
            features: [
                new ToolFeature('calculate_pro_labore', 'Cálculo do pró-labore, retenções e valor líquido', ToolFeatureTier::Essential),
                new ToolFeature('calculate_profit_distribution', 'Apuração do lucro disponível e distribuição entre sócios', ToolFeatureTier::Essential),
                new ToolFeature('calculation_memory', 'Premissas, alertas e memória detalhada do cálculo', ToolFeatureTier::Essential),
                new ToolFeature('multiple_partners', 'Simulação estruturada para múltiplos sócios', ToolFeatureTier::Plus),
                new ToolFeature('multiple_periods', 'Simulação e consolidação de várias competências', ToolFeatureTier::Plus),
                new ToolFeature('multiple_scenarios', 'Comparação de múltiplos cenários de retirada', ToolFeatureTier::Plus),
                new ToolFeature('history', 'Histórico, recuperação e duplicação de simulações autenticadas', ToolFeatureTier::Plus),
                new ToolFeature('professional_export', 'Exportação profissional em CSV, JSON e PDF', ToolFeatureTier::Plus),
            ],
            persistence: new ToolPersistencePolicy(enabled: true, schemaVersion: 1, retentionDays: 180, minimumReadableSchemaVersion: 1),
            export: new ToolExportPolicy(enabled: true, formats: ['csv', 'json', 'pdf']),
            sharing: ToolSharingPolicy::disabled(),
            sensitiveData: ToolSensitiveDataPolicy::none(),
        );
    }

    public function historyPolicy(): ToolHistoryPolicy
    {
        return new ToolHistoryPolicy(
            enabled: true,
            retentionDays: 180,
            inputFields: [
                'competence', 'company_regime', 'partner_label', 'gross_pro_labore', 'dependents',
                'other_official_social_security', 'ownership_percentage', 'accounting_profit',
                'accumulated_losses', 'reserves_and_unavailable_amounts', 'adjustments',
                'prior_distributions', 'intended_distribution',
            ],
            resultFields: ['tool_slug', 'schema_version', 'summary', 'details', 'warnings', 'next_actions'],
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
        return 'tools-calculadora-pro-labore-distribuicao-lucros';
    }
}
