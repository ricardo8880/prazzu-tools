<?php

declare(strict_types=1);

namespace App\Tools\EmploymentModelComparator;

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
            toolSlug: 'comparador-clt-pj-autonomo',
            forms: [
                new ToolAnalyticsForm(
                    key: 'main',
                    steps: ['input'],
                    fields: [
                    new ToolAnalyticsField('clt_gross', 'input', selector: '[name="clt_gross"]'),
                    new ToolAnalyticsField('clt_benefits', 'input', selector: '[name="clt_benefits"]'),
                    new ToolAnalyticsField('clt_employee_deductions', 'input', selector: '[name="clt_employee_deductions"]'),
                    new ToolAnalyticsField('clt_company_burden', 'input', selector: '[name="clt_company_burden"]'),
                    new ToolAnalyticsField('pj_invoice', 'input', selector: '[name="pj_invoice"]'),
                    new ToolAnalyticsField('pj_taxes', 'input', selector: '[name="pj_taxes"]'),
                    new ToolAnalyticsField('pj_expenses', 'input', selector: '[name="pj_expenses"]'),
                    new ToolAnalyticsField('autonomous_gross', 'input', selector: '[name="autonomous_gross"]'),
                    new ToolAnalyticsField('autonomous_deductions', 'input', selector: '[name="autonomous_deductions"]'),
                    new ToolAnalyticsField('autonomous_company_burden', 'input', selector: '[name="autonomous_company_burden"]'),
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
            slug: 'comparador-clt-pj-autonomo',
            name: 'Simulador CLT × PJ × Autônomo',
            description: 'Compare remuneração líquida e custo empresarial nos três modelos.',
            category: ToolCategory::Labor,
            icon: 'bi-arrow-left-right',
            routeName: 'tools.comparador-clt-pj-autonomo.index',
            version: '1.0.0',
            access: ToolAccess::Free,
            status: ToolStatus::Beta,
            position: 150,
            supportsHistory: true,
            storesSensitiveData: false,
            keywords: ['clt pj autônomo', 'pejotização', 'contratação', 'remuneração líquida', 'custo empresa'],
            capabilities: [
                ToolCapability::History,
                ToolCapability::VersionedPersistence,
                ToolCapability::Export,
            ],
            features: [
                new ToolFeature('calculate', 'Solução completa do problema', ToolFeatureTier::Essential),
                new ToolFeature('advanced_productivity', 'Produtividade avançada', ToolFeatureTier::Plus),
            ],
            persistence: new ToolPersistencePolicy(enabled: true, schemaVersion: 1, retentionDays: 365, minimumReadableSchemaVersion: 1),
            export: new ToolExportPolicy(enabled: true, formats: ['csv', 'json', 'pdf']),
            sharing: ToolSharingPolicy::disabled(),
            sensitiveData: ToolSensitiveDataPolicy::none(),
        );
    }

    public function historyPolicy(): ToolHistoryPolicy
    {
        return new ToolHistoryPolicy(enabled: true, retentionDays: 365, inputFields: ['clt_gross', 'clt_benefits', 'clt_employee_deductions', 'clt_company_burden', 'pj_invoice', 'pj_taxes', 'pj_expenses', 'autonomous_gross', 'autonomous_deductions', 'autonomous_company_burden'], resultFields: ['clt_net', 'pj_net', 'autonomous_net', 'highest_net'], sensitiveFields: []);
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
        return 'tools-comparador-clt-pj-autonomo';
    }
}
