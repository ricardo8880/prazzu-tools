<?php

declare(strict_types=1);

namespace App\Tools\PresumedProfitIrpjCsllCalculator;

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
use App\Core\Tools\History\Contracts\HasHistoryPolicy;
use App\Core\Tools\History\Data\ToolHistoryPolicy;
use App\Core\Tools\Infrastructure\Data\ToolExportPolicy;
use App\Core\Tools\Infrastructure\Data\ToolPersistencePolicy;
use App\Core\Tools\Infrastructure\Data\ToolSensitiveDataPolicy;
use App\Core\Tools\Infrastructure\Data\ToolSharingPolicy;

final class Tool implements HasAnalyticsJourney, HasHistoryPolicy, HasToolIntegrations, HasViews, HasWebRoutes, ToolModule
{
    public const SLUG = 'calculadora-irpj-csll-lucro-presumido';

    public function integrations(): ToolIntegrationManifest
    {
        return new ToolIntegrationManifest(publishes: [], accepts: []);
    }

    public function manifest(): ToolManifest
    {
        return new ToolManifest(
            slug: self::SLUG,
            name: 'Calculadora de IRPJ e CSLL — Lucro Presumido',
            description: 'Calcule IRPJ, adicional de IRPJ e CSLL no Lucro Presumido com múltiplas atividades, regra de 2026 e memória de cálculo.',
            category: ToolCategory::Fiscal,
            icon: 'bi-building-check',
            routeName: 'tools.calculadora-irpj-csll-lucro-presumido.index',
            vertical: 'contabilidade',
            version: '1.0.0',
            access: ToolAccess::Free,
            status: ToolStatus::Beta,
            position: 11,
            featured: true,
            supportsHistory: true,
            storesSensitiveData: false,
            keywords: ['lucro presumido', 'IRPJ', 'CSLL', 'adicional IRPJ', 'presunção 2026', 'imposto trimestral'],
            capabilities: [ToolCapability::History, ToolCapability::VersionedPersistence, ToolCapability::Export],
            features: [
                new ToolFeature('calculate', 'IRPJ, adicional e CSLL do trimestre', ToolFeatureTier::Essential),
                new ToolFeature('presumption_2026', 'Acréscimo de 10% nos percentuais de presunção de 2026', ToolFeatureTier::Essential),
                new ToolFeature('memory', 'Memória de cálculo e fontes normativas', ToolFeatureTier::Essential),
                new ToolFeature('periodicity', 'Apuração mensal ou trimestral', ToolFeatureTier::Plus),
                new ToolFeature('multiple_activities', 'Múltiplas atividades no mesmo período', ToolFeatureTier::Plus),
                new ToolFeature('scenario_comparison', 'Comparação de cenários', ToolFeatureTier::Plus),
                new ToolFeature('carry_forward_limit', 'Ajuste do limite com receitas de trimestres anteriores', ToolFeatureTier::Plus),
                new ToolFeature('credits', 'Dedução parametrizada de créditos e retenções confirmados', ToolFeatureTier::Plus),
                new ToolFeature('export', 'Relatório em PDF e planilha', ToolFeatureTier::Plus),
                new ToolFeature('history', 'Histórico autenticado', ToolFeatureTier::Plus),
            ],
            persistence: new ToolPersistencePolicy(true, 1, 365, 1),
            export: new ToolExportPolicy(true, ['pdf', 'xlsx', 'json']),
            sharing: ToolSharingPolicy::disabled(),
            sensitiveData: ToolSensitiveDataPolicy::none(),
        );
    }

    public function historyPolicy(): ToolHistoryPolicy
    {
        return new ToolHistoryPolicy(
            true,
            365,
            ['periodicity', 'month', 'quarter', 'commerceRevenue', 'fuelRevenue', 'passengerTransportRevenue', 'servicesRevenue', 'otherTaxableAdditions', 'priorIrpjPresumptionRevenue', 'priorCsllPresumptionRevenue', 'irpjCredits', 'csllCredits'],
            ['irpj_base', 'irpj_due', 'csll_base', 'csll_due', 'total_due', 'summary', 'details', 'warnings', 'calculation_memory'],
            [],
        );
    }

    public function webRoutesPath(): string { return __DIR__.'/Routes/web.php'; }
    public function viewsPath(): string { return __DIR__.'/Resources/views'; }
    public function viewsNamespace(): string { return 'tools-calculadora-irpj-csll-lucro-presumido'; }

    public function analyticsJourney(): ToolAnalyticsJourney
    {
        return new ToolAnalyticsJourney(
            toolSlug: self::SLUG,
            forms: [new ToolAnalyticsForm(
                key: 'main', steps: ['input'],
                fields: [
                    new ToolAnalyticsField('quarter', 'input', selector: '[name="quarter"]'),
                    new ToolAnalyticsField('commerce_revenue', 'input', selector: '[name="commerce_revenue"]'),
                    new ToolAnalyticsField('fuel_revenue', 'input', selector: '[name="fuel_revenue"]'),
                    new ToolAnalyticsField('passenger_transport_revenue', 'input', selector: '[name="passenger_transport_revenue"]'),
                    new ToolAnalyticsField('services_revenue', 'input', selector: '[name="services_revenue"]'),
                    new ToolAnalyticsField('other_taxable_additions', 'input', selector: '[name="other_taxable_additions"]'),
                ],
                actions: ['calculate', 'export', 'share'],
                selector: 'form[action*="calculadora-irpj-csll-lucro-presumido"]',
                resultSelector: '[data-analytics-result="main"]',
            )],
        );
    }
}
