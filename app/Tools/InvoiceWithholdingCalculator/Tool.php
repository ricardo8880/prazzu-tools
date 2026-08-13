<?php

declare(strict_types=1);

namespace App\Tools\InvoiceWithholdingCalculator;

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
    public const SLUG = 'calculadora-retencoes-nota-fiscal';

    public function integrations(): ToolIntegrationManifest
    {
        return new ToolIntegrationManifest(publishes: [], accepts: []);
    }

    public function manifest(): ToolManifest
    {
        return new ToolManifest(
            slug: self::SLUG, name: 'Calculadora de Retenções na Nota Fiscal', description: 'Estime IRRF, INSS, ISS, PIS/Pasep, Cofins e CSLL com parâmetros revisáveis, memória de cálculo e conferência em lote.', category: ToolCategory::Fiscal, icon: 'bi-receipt-cutoff', routeName: 'tools.calculadora-retencoes-nota-fiscal.index', vertical: 'contabilidade', version: '1.0.0', access: ToolAccess::Free, status: ToolStatus::Beta, position: 14, featured: true, supportsHistory: true, storesSensitiveData: false,
            keywords: ['retenções', 'nota fiscal', 'IRRF', 'INSS', 'ISS', 'PIS', 'COFINS', 'CSLL', 'retenção na fonte'],
            capabilities: [ToolCapability::History, ToolCapability::VersionedPersistence, ToolCapability::Export],
            features: [
                new ToolFeature('calculate', 'Estimativa de retenções de uma nota/serviço', ToolFeatureTier::Essential),
                new ToolFeature('memory', 'Memória de cálculo e premissas', ToolFeatureTier::Plus),
                new ToolFeature('custom_rules', 'Bases e alíquotas configuráveis por tributo', ToolFeatureTier::Plus),
                new ToolFeature('multiple_notes', 'Múltiplas notas ou serviços no mesmo cálculo', ToolFeatureTier::Plus),
                new ToolFeature('report', 'Relatório de conferência por nota e tributo', ToolFeatureTier::Plus),
                new ToolFeature('export', 'Exportação PDF e XLSX', ToolFeatureTier::Plus),
                new ToolFeature('history', 'Histórico autenticado', ToolFeatureTier::Plus),
            ],
            persistence: new ToolPersistencePolicy(true, 1, 365, 1), export: new ToolExportPolicy(true, ['pdf', 'xlsx', 'json']), sharing: ToolSharingPolicy::disabled(), sensitiveData: ToolSensitiveDataPolicy::none(),
        );
    }

    public function historyPolicy(): ToolHistoryPolicy
    {
        return new ToolHistoryPolicy(true, 365, ['competence', 'invoice_number', 'service_description', 'gross_value', 'apply_irrf', 'irrf_rate', 'irrf_base_percent', 'apply_inss', 'inss_rate', 'inss_base_percent', 'apply_iss', 'iss_rate', 'iss_base_percent', 'apply_pis', 'pis_rate', 'pis_base_percent', 'apply_cofins', 'cofins_rate', 'cofins_base_percent', 'apply_csll', 'csll_rate', 'csll_base_percent', 'notes'], ['gross_minor', 'total_withheld_minor', 'net_minor', 'taxes', 'notes', 'summary', 'details', 'warnings', 'calculation_memory'], []);
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
        return 'tools-calculadora-retencoes-nota-fiscal';
    }

    public function analyticsJourney(): ToolAnalyticsJourney
    {
        return new ToolAnalyticsJourney(toolSlug: self::SLUG, forms: [new ToolAnalyticsForm(key: 'main', steps: ['input', 'rules', 'plus'], fields: [
            new ToolAnalyticsField('competence', 'input', selector: '[name="competence"]'), new ToolAnalyticsField('gross_value', 'input', selector: '[name="gross_value"]'), new ToolAnalyticsField('apply_irrf', 'rules', selector: '[name="apply_irrf"]'), new ToolAnalyticsField('apply_inss', 'rules', selector: '[name="apply_inss"]'), new ToolAnalyticsField('apply_iss', 'rules', selector: '[name="apply_iss"]'), new ToolAnalyticsField('apply_pis', 'rules', selector: '[name="apply_pis"]'), new ToolAnalyticsField('apply_cofins', 'rules', selector: '[name="apply_cofins"]'), new ToolAnalyticsField('apply_csll','rules',selector: '[name="apply_csll"]'),
        ], actions: ['calculate', 'export'], selector: 'form[action*="calculadora-retencoes-nota-fiscal"]', resultSelector: '[data-analytics-result="main"]')]);
    }
}
