<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreProfitDistributionCalculator;

use App\Core\Tools\Analytics\Contracts\HasAnalyticsJourney;
use App\Core\Tools\Analytics\Data\ToolAnalyticsField;
use App\Core\Tools\Analytics\Data\ToolAnalyticsForm;
use App\Core\Tools\Analytics\Data\ToolAnalyticsJourney;
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

final class Tool implements HasAnalyticsJourney, HasApiActions, HasHistoryPolicy, HasToolIntegrations, HasViews, HasWebRoutes, ToolModule
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

    public function analyticsJourney(): ToolAnalyticsJourney
    {
        return new ToolAnalyticsJourney(
            toolSlug: 'calculadora-pro-labore-distribuicao-lucros',
            forms: [
                new ToolAnalyticsForm(
                    key: 'main',
                    steps: ['input'],
                    fields: [
                    new ToolAnalyticsField('competence', 'input', selector: '[name="competence"]'),
                    new ToolAnalyticsField('company_regime', 'input', selector: '[name="company_regime"]'),
                    new ToolAnalyticsField('partner_label', 'input', selector: '[name="partner_label"]'),
                    new ToolAnalyticsField('ownership_percentage', 'input', selector: '[name="ownership_percentage"]'),
                    new ToolAnalyticsField('dependents', 'input', selector: '[name="dependents"]'),
                    new ToolAnalyticsField('gross_pro_labore', 'input', selector: '[name="gross_pro_labore"]'),
                    new ToolAnalyticsField('other_official_social_security', 'input', selector: '[name="other_official_social_security"]'),
                    new ToolAnalyticsField('accounting_profit', 'input', selector: '[name="accounting_profit"]'),
                    new ToolAnalyticsField('accumulated_losses', 'input', selector: '[name="accumulated_losses"]'),
                    new ToolAnalyticsField('reserves_and_unavailable_amounts', 'input', selector: '[name="reserves_and_unavailable_amounts"]'),
                    new ToolAnalyticsField('adjustments', 'input', selector: '[name="adjustments"]'),
                    new ToolAnalyticsField('prior_distributions', 'input', selector: '[name="prior_distributions"]'),
                    new ToolAnalyticsField('intended_distribution', 'input', selector: '[name="intended_distribution"]'),
                    new ToolAnalyticsField('confirm_assumptions', 'input', selector: '[name="confirm_assumptions"]'),
                    ],
                    actions: ['calculate', 'export', 'share'],
                    selector: 'form[action*="calculate"]',
                    resultSelector: '[data-analytics-result="main"]',
                ),
            ],
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
            vertical: 'contabilidade',
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
