<?php

declare(strict_types=1);

namespace App\Tools\TurnoverCalculator;

use App\Core\Tools\Analytics\Contracts\HasAnalyticsJourney;
use App\Core\Tools\Analytics\Data\ToolAnalyticsField;
use App\Core\Tools\Analytics\Data\ToolAnalyticsForm;
use App\Core\Tools\Analytics\Data\ToolAnalyticsJourney;
use App\Core\ToolIntegration\Data\ToolIntegrationManifest;
use App\Core\Tools\Contracts\HasToolIntegrations;
use App\Core\Tools\Contracts\HasViews;
use App\Core\Tools\Contracts\HasWebRoutes;
use App\Core\Tools\Contracts\ToolModule;
use App\Core\Tools\Data\ToolFeature;
use App\Core\Tools\Data\ToolManifest;
use App\Core\Tools\Enums\ToolAccess;
use App\Core\Tools\Enums\ToolCategory;
use App\Core\Tools\Enums\ToolFeatureTier;
use App\Core\Tools\Enums\ToolStatus;
use App\Core\Tools\Infrastructure\Data\ToolExportPolicy;
use App\Core\Tools\Infrastructure\Data\ToolPersistencePolicy;
use App\Core\Tools\Infrastructure\Data\ToolSensitiveDataPolicy;
use App\Core\Tools\Infrastructure\Data\ToolSharingPolicy;

final class Tool implements HasAnalyticsJourney, HasToolIntegrations, HasViews, HasWebRoutes, ToolModule
{
    public const SLUG = 'calculadora-turnover';

    public function integrations(): ToolIntegrationManifest
    {
        return new ToolIntegrationManifest(publishes: [], accepts: []);
    }

    public function analyticsJourney(): ToolAnalyticsJourney
    {
        return new ToolAnalyticsJourney(
            toolSlug: self::SLUG,
            forms: [
                new ToolAnalyticsForm(
                    key: 'main',
                    steps: ['input'],
                    fields: [
                        new ToolAnalyticsField('admissions', 'input', selector: '[name="admissions"]'),
                        new ToolAnalyticsField('terminations', 'input', selector: '[name="terminations"]'),
                        new ToolAnalyticsField('average_headcount', 'input', selector: '[name="average_headcount"]'),
                    ],
                    actions: ['calculate'],
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
            name: 'Calculadora de Turnover',
            description: 'Calcule a taxa de turnover a partir de admissões, desligamentos e quadro médio do período.',
            category: ToolCategory::Labor,
            icon: 'bi-people',
            routeName: 'tools.calculadora-turnover.index',
            vertical: 'rh',
            version: '1.0.0',
            access: ToolAccess::Free,
            status: ToolStatus::Beta,
            position: 10,
            featured: true,
            supportsHistory: false,
            storesSensitiveData: false,
            keywords: ['turnover', 'rotatividade', 'recursos humanos', 'rh', 'gestão de pessoas'],
            features: [
                new ToolFeature('calculate', 'Taxa de turnover do período', ToolFeatureTier::Essential),
                new ToolFeature('method', 'Memória transparente da fórmula utilizada', ToolFeatureTier::Essential),
                new ToolFeature('advanced_analysis', 'Análises avançadas por período e segmento', ToolFeatureTier::Plus),
            ],
            capabilities: [],
            persistence: ToolPersistencePolicy::disabled(),
            export: ToolExportPolicy::disabled(),
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
        return 'tools-calculadora-turnover';
    }
}
