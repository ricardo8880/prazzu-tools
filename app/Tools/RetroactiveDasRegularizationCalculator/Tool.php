<?php

declare(strict_types=1);

namespace App\Tools\RetroactiveDasRegularizationCalculator;

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
    public const SLUG = 'calculadora-das-retroativo-regularizacao-simples';

    public function integrations(): ToolIntegrationManifest
    {
        return new ToolIntegrationManifest(publishes: [], accepts: []);
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
        return 'tools-calculadora-das-retroativo-regularizacao-simples';
    }

    public function manifest(): ToolManifest
    {
        return new ToolManifest(slug: self::SLUG, name: 'Calculadora de DAS Retroativo + Regularização do Simples', description: 'Reconstitua um DAS estimado por competência e faturamento, atualize multa/juros e planeje a regularização de várias competências.', category: ToolCategory::Fiscal, icon: 'bi-calendar2-week', routeName: 'tools.calculadora-das-retroativo-regularizacao-simples.index', vertical: 'contabilidade', version: '1.0.0', access: ToolAccess::Free, status: ToolStatus::Beta, position: 205, featured: true, supportsHistory: false, storesSensitiveData: false, keywords: ['das retroativo', 'simples nacional', 'regularização', 'competência', 'multa', 'selic'], capabilities: [ToolCapability::Export], features: [new ToolFeature('calculate', 'Competência, faturamento, vencimento e parâmetros para principal, multa, juros e total', ToolFeatureTier::Essential), new ToolFeature('memory', 'Memória normativa do cálculo principal', ToolFeatureTier::Essential), new ToolFeature('multiple_competencies', 'Várias competências e dívida consolidada', ToolFeatureTier::Plus), new ToolFeature('regularization', 'Cronograma e cenários de regularização financeira', ToolFeatureTier::Plus), new ToolFeature('report', 'Relatório PDF/XLSX', ToolFeatureTier::Plus)], persistence: ToolPersistencePolicy::disabled(), export: new ToolExportPolicy(enabled: true, formats: ['pdf', 'xlsx']), sharing: ToolSharingPolicy::disabled(), sensitiveData: ToolSensitiveDataPolicy::none());
    }

    public function analyticsJourney(): ToolAnalyticsJourney
    {
        return new ToolAnalyticsJourney(toolSlug: self::SLUG, forms: [new ToolAnalyticsForm(key: 'main', steps: ['competence', 'regularization'], fields: [new ToolAnalyticsField('competence', 'competence', selector: '[name="competence"]'), new ToolAnalyticsField('revenue', 'competence', selector: '[name="revenue"]'), new ToolAnalyticsField('effective_rate', 'competence', selector: '[name="effective_rate"]'), new ToolAnalyticsField('due_date', 'competence', selector: '[name="due_date"]'), new ToolAnalyticsField('regularization_months','regularization',selector: '[name="regularization_months"]')], actions: ['calculate', 'export'], selector: 'form[action*="calculadora-das-retroativo-regularizacao-simples"]', resultSelector: '[data-analytics-result="main"]')]);
    }
}
