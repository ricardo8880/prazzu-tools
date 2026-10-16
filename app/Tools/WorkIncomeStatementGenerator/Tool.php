<?php

declare(strict_types=1);

namespace App\Tools\WorkIncomeStatementGenerator;

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
use App\Core\Tools\Infrastructure\Enums\SensitiveDataMode;

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
            toolSlug: 'employment-income-letter-generator',
            forms: [
                new ToolAnalyticsForm(
                    key: 'main',
                    steps: ['input'],
                    fields: [
                        new ToolAnalyticsField('start_date', 'input', selector: '[name="start_date"]'),
                        new ToolAnalyticsField('monthly_income', 'input', selector: '[name="monthly_income"]'),
                        new ToolAnalyticsField('issue_date', 'input', selector: '[name="issue_date"]'),
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
            slug: 'employment-income-letter-generator',
            name: 'Employment Income Letter Generator',
            description: 'Generate an informational employment and income verification letter from user-entered data.',
            category: ToolCategory::Documents,
            icon: 'bi-file-earmark-check',
            routeName: 'tools.employment-income-letter-generator.index',
            vertical: 'contabilidade',
            version: '1.1.0',
            access: ToolAccess::Free,
            status: ToolStatus::Beta,
            position: 250,
            supportsHistory: true,
            storesSensitiveData: true,
            keywords: ['employment income letter', 'income verification', 'employment verification', 'proof of income'],
            capabilities: [
                ToolCapability::History,
                ToolCapability::VersionedPersistence,
                ToolCapability::Export,
                ToolCapability::SensitiveData,
            ],
            features: [
                new ToolFeature('calculate', 'Full calculation', ToolFeatureTier::Essential),
                new ToolFeature('spreadsheet_export', 'Advanced Excel export', ToolFeatureTier::Plus),
            ],
            persistence: new ToolPersistencePolicy(enabled: true, schemaVersion: 1, retentionDays: 365, minimumReadableSchemaVersion: 1),
            export: new ToolExportPolicy(enabled: true, formats: ['json', 'pdf', 'xlsx']),
            sharing: ToolSharingPolicy::disabled(),
            sensitiveData: new ToolSensitiveDataPolicy(SensitiveDataMode::Encrypted, ['name', 'document']),
        );
    }

    public function historyPolicy(): ToolHistoryPolicy
    {
        return new ToolHistoryPolicy(enabled: true, retentionDays: 365, inputFields: ['name', 'document', 'employer', 'occupation', 'start_date', 'monthly_income', 'city', 'issue_date'], resultFields: ['monthly_income', 'worker', 'occupation'], sensitiveFields: ['name', 'document']);
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
        return 'tools-employment-income-letter-generator';
    }
}
