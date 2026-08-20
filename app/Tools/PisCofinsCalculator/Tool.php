<?php

declare(strict_types=1);

namespace App\Tools\PisCofinsCalculator;

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
use App\Core\Tools\History\Contracts\ProvidesHistoryContext;
use App\Core\Tools\History\Data\ToolHistoryPolicy;
use App\Core\Tools\History\Support\HistoryPeriodFormatter;
use App\Core\Tools\Infrastructure\Data\ToolExportPolicy;
use App\Core\Tools\Infrastructure\Data\ToolPersistencePolicy;
use App\Core\Tools\Infrastructure\Data\ToolSensitiveDataPolicy;
use App\Core\Tools\Infrastructure\Data\ToolSharingPolicy;
use DateTimeImmutable;

final class Tool implements HasAnalyticsJourney, HasHistoryPolicy, ProvidesHistoryContext, HasToolIntegrations, HasViews, HasWebRoutes, ToolModule
{
    public const SLUG = 'calculadora-pis-cofins';

    public function integrations(): ToolIntegrationManifest
    {
        return new ToolIntegrationManifest(publishes: [], accepts: []);
    }

    public function manifest(): ToolManifest
    {
        return new ToolManifest(
            slug: self::SLUG,
            name: 'Calculadora PIS e COFINS',
            description: 'Calcule PIS/Pasep e Cofins nos regimes cumulativo e não cumulativo, com créditos, comparação entre regimes e memória de cálculo.',
            category: ToolCategory::Fiscal,
            icon: 'bi-percent',
            routeName: 'tools.calculadora-pis-cofins.index',
            vertical: 'contabilidade',
            version: '1.0.0',
            access: ToolAccess::Free,
            status: ToolStatus::Beta,
            position: 12,
            featured: true,
            supportsHistory: true,
            storesSensitiveData: false,
            keywords: ['PIS', 'COFINS', 'PIS/Pasep', 'regime cumulativo', 'não cumulativo', 'créditos PIS Cofins', 'apuração mensal'],
            capabilities: [ToolCapability::History, ToolCapability::VersionedPersistence, ToolCapability::Export],
            features: [
                new ToolFeature('calculate', 'Apuração completa de PIS e Cofins em um regime', ToolFeatureTier::Essential),
                new ToolFeature('aggregate_credits', 'Créditos do regime não cumulativo', ToolFeatureTier::Plus),
                new ToolFeature('memory', 'Memória de cálculo e fontes normativas', ToolFeatureTier::Essential),
                new ToolFeature('multiple_operations', 'Múltiplas operações na mesma competência', ToolFeatureTier::Plus),
                new ToolFeature('credit_breakdown', 'Créditos detalhados por operação', ToolFeatureTier::Plus),
                new ToolFeature('comparison', 'Comparação cumulativo × não cumulativo', ToolFeatureTier::Plus),
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
        return new ToolHistoryPolicy(true, 365, ['period', 'regime', 'compare_regimes', 'taxable_revenue', 'credit_base', 'pis_withheld', 'cofins_withheld', 'operations'], ['pis_payable', 'cofins_payable', 'total_payable', 'effective_rate', 'summary', 'details', 'warnings', 'calculation_memory'], []);
    }

    public function historyContext(array $input, DateTimeImmutable $referenceDate): ?string
    {
        return HistoryPeriodFormatter::yearMonth($input['period'] ?? null);
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
        return 'tools-calculadora-pis-cofins';
    }

    public function analyticsJourney(): ToolAnalyticsJourney
    {
        return new ToolAnalyticsJourney(toolSlug: self::SLUG, forms: [new ToolAnalyticsForm(
            key: 'main', steps: ['input', 'advanced'], fields: [
                new ToolAnalyticsField('period', 'input', selector: '[name="period"]'),
                new ToolAnalyticsField('regime', 'input', selector: '[name="regime"]'),
                new ToolAnalyticsField('taxable_revenue', 'input', selector: '[name="taxable_revenue"]'),
                new ToolAnalyticsField('credit_base', 'input', selector: '[name="credit_base"]'),
                new ToolAnalyticsField('compare_regimes', 'advanced', selector: '[name="compare_regimes"]'),
            ], actions: ['calculate', 'export'], selector: 'form[action*="calculadora-pis-cofins"]', resultSelector: '[data-analytics-result="main"]'
        )]);
    }
}
