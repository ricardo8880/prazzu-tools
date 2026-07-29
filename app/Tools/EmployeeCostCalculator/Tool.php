<?php

declare(strict_types=1);

namespace App\Tools\EmployeeCostCalculator;

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
use App\Core\Tools\Infrastructure\Enums\SensitiveDataMode;

final class Tool implements HasAnalyticsJourney, HasHistoryPolicy, HasToolIntegrations, HasViews, HasWebRoutes, ToolModule
{
    public const SLUG = 'custo-funcionario-clt';

    public function integrations(): ToolIntegrationManifest
    {
        return new ToolIntegrationManifest(publishes: [], accepts: []);
    }

    public function analyticsJourney(): ToolAnalyticsJourney
    {
        return new ToolAnalyticsJourney(
            toolSlug: 'custo-funcionario-clt',
            forms: [
                new ToolAnalyticsForm(
                    key: 'main',
                    steps: ['input'],
                    fields: [
                    new ToolAnalyticsField('employee_name', 'input', selector: '[name="employee_name"]'),
                    new ToolAnalyticsField('department', 'input', selector: '[name="department"]'),
                    new ToolAnalyticsField('company_profile_id', 'input', selector: '[name="company_profile_id"]'),
                    new ToolAnalyticsField('scenario_name', 'input', selector: '[name="scenario_name"]'),
                    new ToolAnalyticsField('regime', 'input', selector: '[name="regime"]'),
                    new ToolAnalyticsField('rat', 'input', selector: '[name="rat"]'),
                    new ToolAnalyticsField('third_parties', 'input', selector: '[name="third_parties"]'),
                    new ToolAnalyticsField('monthly_hours', 'input', selector: '[name="monthly_hours"]'),
                    new ToolAnalyticsField('import_file', 'input', selector: '[name="import_file"]'),
                    new ToolAnalyticsField('import_token', 'input', selector: '[name="import_token"]'),
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
            name: 'Calculadora de Custo de Funcionário CLT',
            description: 'Calcule salário, benefícios, encargos, provisões e custo mensal, anual e por hora.',
            category: ToolCategory::Labor,
            icon: 'bi-person-vcard',
            routeName: 'tools.custo-funcionario-clt.index',
            version: '1.1.0',
            access: ToolAccess::Free,
            status: ToolStatus::Beta,
            position: 110,
            supportsHistory: true,
            storesSensitiveData: true,
            keywords: ['custo funcionário', 'custo CLT', 'encargos funcionário', 'folha de pagamento', 'custo empregador', 'FGTS'],
            capabilities: [
                ToolCapability::History,
                ToolCapability::VersionedPersistence,
                ToolCapability::Export,
                ToolCapability::SensitiveData,
            ],
            features: [
                new ToolFeature('calculate', 'Cálculo individual completo e memória', ToolFeatureTier::Essential),
                new ToolFeature('print_report', 'Relatório individual para impressão e PDF', ToolFeatureTier::Essential),
                new ToolFeature('history', 'Histórico, reabertura, duplicação e exclusão', ToolFeatureTier::Plus),
                new ToolFeature('company_profiles', 'Perfis reutilizáveis de empresas e escritório', ToolFeatureTier::Plus),
                new ToolFeature('employee_profiles', 'Perfis reutilizáveis de funcionários', ToolFeatureTier::Plus),
                new ToolFeature('batch_processing', 'Cálculo de múltiplos funcionários e folha consolidada', ToolFeatureTier::Plus),
                new ToolFeature('csv_import', 'Importação validada de funcionários por CSV', ToolFeatureTier::Plus),
                new ToolFeature('xlsx_import', 'Importação validada de funcionários por XLSX', ToolFeatureTier::Plus),
                new ToolFeature('scenarios', 'Criação e comparação de cenários', ToolFeatureTier::Plus),
                new ToolFeature('employment_model_comparison', 'Comparação numérica CLT × PJ × Autônomo', ToolFeatureTier::Plus),
                new ToolFeature('csv_export', 'Exportação de relatórios em CSV', ToolFeatureTier::Plus),
                new ToolFeature('xlsx_export', 'Exportação de relatórios em XLSX', ToolFeatureTier::Plus),
                new ToolFeature('professional_report', 'Relatório consolidado e por departamento', ToolFeatureTier::Plus),
                new ToolFeature('branded_report', 'Relatório com dados do escritório', ToolFeatureTier::Plus),
                new ToolFeature('projections', 'Projeção mensal e anual de custos', ToolFeatureTier::Plus),
            ],
            persistence: new ToolPersistencePolicy(
                enabled: true,
                schemaVersion: 1,
                retentionDays: 365,
                minimumReadableSchemaVersion: 1,
            ),
            export: new ToolExportPolicy(enabled: true, formats: ['csv', 'json', 'pdf', 'xlsx']),
            sharing: ToolSharingPolicy::disabled(),
            sensitiveData: new ToolSensitiveDataPolicy(
                SensitiveDataMode::Encrypted,
                ['employee_name', 'employees', 'document'],
            ),
        );
    }

    public function historyPolicy(): ToolHistoryPolicy
    {
        return new ToolHistoryPolicy(
            enabled: true,
            retentionDays: 365,
            inputFields: [
                'employee_name',
                'department',
                'scenario_name',
                'company_profile_id',
                'salary',
                'variable_pay',
                'benefits',
                'regime',
                'rat',
                'third_parties',
                'monthly_hours',
                'employees',
            ],
            resultFields: ['calculation_type', 'result', 'batch'],
            sensitiveFields: ['employee_name', 'employees'],
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
        return 'tools-custo-funcionario-clt';
    }
}
