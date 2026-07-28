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
            name: 'Planejador de Retirada de Sócios',
            description: 'Planeje a composição da retirada de um sócio entre pró-labore e distribuição de lucros, consolidando valor líquido, custo empresarial e saldo de lucro.',
            category: ToolCategory::Fiscal,
            icon: 'bi-cash-coin',
            routeName: 'tools.calculadora-pro-labore-distribuicao-lucros.index',
            version: '3.1.0',
            access: ToolAccess::Free,
            status: ToolStatus::Active,
            position: 30,
            featured: false,
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
                new ToolFeature('withdrawal_composition', 'Composição consolidada entre pró-labore e lucro distribuído', ToolFeatureTier::Essential),
                new ToolFeature('company_partner_impact', 'Visão conjunta do líquido do sócio, custo empresarial e lucro remanescente', ToolFeatureTier::Essential),
                new ToolFeature('scenario_planning', 'Comparação de múltiplos cenários, sócios e competências', ToolFeatureTier::Plus),
                new ToolFeature('history_exports', 'Histórico autenticado e exportações do planejamento', ToolFeatureTier::Plus),
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
