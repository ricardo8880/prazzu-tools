<?php

declare(strict_types=1);

namespace App\Tools\MeiToMicroenterpriseSimulator;

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
    public const SLUG = 'simulador-mei-microempresa';

    public function integrations(): ToolIntegrationManifest { return new ToolIntegrationManifest(publishes: [], accepts: []); }

    public function manifest(): ToolManifest
    {
        return new ToolManifest(
            slug: self::SLUG,
            name: 'Simulador MEI → Microempresa',
            description: 'Compare o faturamento atual e projetado com o limite do MEI e estime o impacto econômico de uma migração para Microempresa.',
            category: ToolCategory::Calculators,
            icon: 'bi-arrow-up-right-square',
            routeName: 'tools.simulador-mei-microempresa.index',
            vertical: 'contabilidade',
            version: '1.0.0',
            access: ToolAccess::Free,
            status: ToolStatus::Beta,
            position: 205,
            featured: true,
            supportsHistory: false,
            storesSensitiveData: false,
            keywords: ['mei', 'microempresa', 'desenquadramento mei', 'faturamento mei', 'migração mei', 'simples nacional'],
            capabilities: [ToolCapability::Export],
            features: [
                new ToolFeature('calculate', 'Faturamento atual/projetado e impacto estimado de sair do MEI', ToolFeatureTier::Essential),
                new ToolFeature('memory', 'Memória de cálculo e faixa de excesso do limite', ToolFeatureTier::Essential),
                new ToolFeature('annual_projection', 'Projeção anual de faturamento e custos', ToolFeatureTier::Plus),
                new ToolFeature('business_costs', 'Impostos e custos empresariais parametrizados', ToolFeatureTier::Plus),
                new ToolFeature('migration_point', 'Ponto em que os custos fixos da migração pesam menos', ToolFeatureTier::Plus),
                new ToolFeature('report', 'Relatório e planilha da projeção', ToolFeatureTier::Plus),
            ],
            persistence: ToolPersistencePolicy::disabled(),
            export: new ToolExportPolicy(enabled: true, formats: ['pdf', 'xlsx']),
            sharing: ToolSharingPolicy::disabled(),
            sensitiveData: ToolSensitiveDataPolicy::none(),
        );
    }

    public function webRoutesPath(): string { return __DIR__.'/Routes/web.php'; }
    public function viewsPath(): string { return __DIR__.'/Resources/views'; }
    public function viewsNamespace(): string { return 'tools-simulador-mei-microempresa'; }

    public function analyticsJourney(): ToolAnalyticsJourney
    {
        return new ToolAnalyticsJourney(
            toolSlug: self::SLUG,
            forms: [new ToolAnalyticsForm(
                key: 'main',
                steps: ['revenue', 'plus'],
                fields: [
                    new ToolAnalyticsField('current_annual_revenue', 'revenue', selector: '[name="current_annual_revenue"]'),
                    new ToolAnalyticsField('projected_annual_revenue', 'revenue', selector: '[name="projected_annual_revenue"]'),
                    new ToolAnalyticsField('me_effective_tax_rate', 'plus', selector: '[name="me_effective_tax_rate"]'),
                    new ToolAnalyticsField('monthly_accounting_cost', 'plus', selector: '[name="monthly_accounting_cost"]'),
                ],
                actions: ['calculate', 'export'],
                selector: 'form[action*="simulador-mei-microempresa"]',
                resultSelector: '[data-analytics-result="main"]',
            )],
        );
    }
}
