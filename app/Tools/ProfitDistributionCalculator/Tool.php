<?php

declare(strict_types=1);

namespace App\Tools\ProfitDistributionCalculator;

use App\Core\Tools\Analytics\Contracts\HasAnalyticsJourney;
use App\Core\Tools\Analytics\Data\ToolAnalyticsField;
use App\Core\Tools\Analytics\Data\ToolAnalyticsForm;
use App\Core\Tools\Analytics\Data\ToolAnalyticsJourney;
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

final class Tool implements HasAnalyticsJourney, HasHistoryPolicy, HasViews, HasWebRoutes, ToolModule
{
    public const SLUG = 'distribuicao-de-lucros';

    public function analyticsJourney(): ToolAnalyticsJourney
    {
        return new ToolAnalyticsJourney(
            toolSlug: 'distribuicao-de-lucros',
            forms: [
                new ToolAnalyticsForm(
                    key: 'main',
                    steps: ['input'],
                    fields: [
                    new ToolAnalyticsField('partner_label', 'input', selector: '[name="partner_label"]'),
                    new ToolAnalyticsField('ownership_percentage', 'input', selector: '[name="ownership_percentage"]'),
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
            name: 'Calculadora de Distribuição de Lucros',
            description: 'Calcule o lucro disponível, a distribuição por participação societária e o saldo remanescente.',
            category: ToolCategory::Fiscal,
            icon: 'bi-pie-chart',
            routeName: 'tools.distribuicao-de-lucros.index',
            vertical: 'contabilidade',
            version: '1.0.0',
            access: ToolAccess::Free,
            status: ToolStatus::Beta,
            position: 31,
            featured: true,
            supportsHistory: true,
            keywords: ['distribuição de lucros', 'lucros e dividendos', 'participação societária'],
            capabilities: [
                ToolCapability::History,
                ToolCapability::VersionedPersistence,
                ToolCapability::Export,
            ],
            features: [
                new ToolFeature('calculate', 'Distribuição proporcional completa', ToolFeatureTier::Essential),
                new ToolFeature('memory', 'Memória do lucro disponível', ToolFeatureTier::Essential),
                new ToolFeature('partners', 'Múltiplos sócios, exercícios e cenários', ToolFeatureTier::Plus),
            ],
            persistence: new ToolPersistencePolicy(enabled: true, schemaVersion: 1, retentionDays: 365, minimumReadableSchemaVersion: 1),
            export: new ToolExportPolicy(enabled: true, formats: ['csv', 'json', 'pdf']),
            sharing: ToolSharingPolicy::disabled(),
            sensitiveData: ToolSensitiveDataPolicy::none(),
        );
    }

    public function historyPolicy(): ToolHistoryPolicy
    {
        return new ToolHistoryPolicy(
            enabled: true,
            retentionDays: 365,
            inputFields: [],
            resultFields: ['tool_slug', 'schema_version', 'summary', 'details', 'warnings', 'next_actions', 'calculation_memory'],
            sensitiveFields: [],
        );
    }

    public function webRoutesPath(): string { return __DIR__.'/Routes/web.php'; }
    public function viewsPath(): string { return __DIR__.'/Resources/views'; }
    public function viewsNamespace(): string { return 'tools-distribuicao-de-lucros'; }
}
