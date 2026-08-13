<?php

declare(strict_types=1);

namespace App\Tools\TaxInstallmentCalculator;

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
    public const SLUG = 'calculadora-parcelamento-tributario';

    public function integrations(): ToolIntegrationManifest
    {
        return new ToolIntegrationManifest(publishes: [], accepts: []);
    }

    public function manifest(): ToolManifest
    {
        return new ToolManifest(
            slug: self::SLUG,
            name: 'Calculadora de Parcelamento Tributário',
            description: 'Simule parcelas de uma dívida tributária com encargos informados e compare entrada, prazos, saldo e custo final.',
            category: ToolCategory::Calculators,
            icon: 'bi-calendar2-check',
            routeName: 'tools.calculadora-parcelamento-tributario.index',
            vertical: 'contabilidade',
            version: '1.0.0',
            access: ToolAccess::Free,
            status: ToolStatus::Beta,
            position: 200,
            featured: true,
            supportsHistory: false,
            storesSensitiveData: false,
            keywords: ['parcelamento tributário', 'dívida tributária', 'parcelas', 'encargos', 'saldo devedor', 'tributos'],
            capabilities: [ToolCapability::Export],
            features: [
                new ToolFeature('calculate', 'Dívida, parcelas e encargos com parcela média e custo final', ToolFeatureTier::Essential),
                new ToolFeature('memory', 'Memória de cálculo do cenário principal', ToolFeatureTier::Essential),
                new ToolFeature('scenario_comparison', 'Comparação de entrada, prazos e encargos', ToolFeatureTier::Plus),
                new ToolFeature('balance_evolution', 'Evolução mensal do saldo e cronograma', ToolFeatureTier::Plus),
                new ToolFeature('report', 'Relatório com cenários e cronograma', ToolFeatureTier::Plus),
                new ToolFeature('export', 'Exportação em PDF e XLSX', ToolFeatureTier::Plus),
            ],
            persistence: ToolPersistencePolicy::disabled(),
            export: new ToolExportPolicy(enabled: true, formats: ['pdf', 'xlsx']),
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
        return 'tools-calculadora-parcelamento-tributario';
    }

    public function analyticsJourney(): ToolAnalyticsJourney
    {
        return new ToolAnalyticsJourney(
            toolSlug: self::SLUG,
            forms: [new ToolAnalyticsForm(
                key: 'main',
                steps: ['debt', 'plus'],
                fields: [
                    new ToolAnalyticsField('debt_amount', 'debt', selector: '[name="debt_amount"]'),
                    new ToolAnalyticsField('installments', 'debt', selector: '[name="installments"]'),
                    new ToolAnalyticsField('monthly_charge', 'debt', selector: '[name="monthly_charge"]'),
                    new ToolAnalyticsField('entry_amount', 'plus', selector: '[name="entry_amount"]'),
                ],
                actions: ['calculate', 'export'],
                selector: 'form[action*="calculadora-parcelamento-tributario"]',
                resultSelector: '[data-analytics-result="main"]',
            )],
        );
    }
}
