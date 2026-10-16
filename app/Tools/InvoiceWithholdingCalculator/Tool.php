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
    public const SLUG = 'backup-withholding-calculator';

    public function integrations(): ToolIntegrationManifest
    {
        return new ToolIntegrationManifest(publishes: [], accepts: []);
    }

    public function manifest(): ToolManifest
    {
        return new ToolManifest(
            slug: self::SLUG, name: 'Backup Withholding Calculator', description: 'Estimate U.S. federal backup withholding and other user-entered withholding on reportable payments.', category: ToolCategory::Fiscal, icon: 'bi-receipt-cutoff', routeName: 'tools.backup-withholding-calculator.index', vertical: 'contabilidade', version: '1.0.0', access: ToolAccess::Free, status: ToolStatus::Active, position: 14, featured: true, supportsHistory: true, storesSensitiveData: false,
            keywords: ['backup withholding', '24 percent withholding', 'TIN withholding', 'IRS withholding'],
            capabilities: [ToolCapability::History, ToolCapability::VersionedPersistence, ToolCapability::Export],
            features: [
                new ToolFeature('calculate', 'Federal backup withholding estimate', ToolFeatureTier::Essential),
                new ToolFeature('memory', 'Calculation details and assumptions', ToolFeatureTier::Essential),
                new ToolFeature('state_other_withholding', 'User-entered state and other withholding amounts', ToolFeatureTier::Essential),
                new ToolFeature('report', 'Withholding breakdown', ToolFeatureTier::Essential),
                new ToolFeature('export', 'PDF and XLSX export', ToolFeatureTier::Plus),
                new ToolFeature('history', 'Saved calculation history', ToolFeatureTier::Plus),
            ],
            persistence: new ToolPersistencePolicy(true, 1, 365, 1), export: new ToolExportPolicy(true, ['pdf', 'xlsx', 'json']), sharing: ToolSharingPolicy::disabled(), sensitiveData: ToolSensitiveDataPolicy::none(),
        );
    }

    public function historyPolicy(): ToolHistoryPolicy
    {
        return new ToolHistoryPolicy(true, 365, ['competence', 'payment_reference', 'payment_description', 'gross_value', 'apply_backup_withholding', 'backup_withholding_rate', 'state_withholding', 'other_withholding'], ['gross_minor', 'backup_withholding_minor', 'state_withholding_minor', 'other_withholding_minor', 'total_withheld_minor', 'net_minor', 'summary', 'details', 'warnings', 'calculation_memory'], []);
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
        return 'tools-backup-withholding-calculator';
    }

    public function analyticsJourney(): ToolAnalyticsJourney
    {
        return new ToolAnalyticsJourney(toolSlug: self::SLUG, forms: [new ToolAnalyticsForm(key: 'main', steps: ['input', 'rules', 'plus'], fields: [
            new ToolAnalyticsField('competence', 'input', selector: '[name="competence"]'), new ToolAnalyticsField('gross_value', 'input', selector: '[name="gross_value"]'), new ToolAnalyticsField('apply_backup_withholding', 'rules', selector: '[name="apply_backup_withholding"]'), new ToolAnalyticsField('backup_withholding_rate', 'rules', selector: '[name="backup_withholding_rate"]'), new ToolAnalyticsField('state_withholding', 'rules', selector: '[name="state_withholding"]'), new ToolAnalyticsField('other_withholding', 'rules', selector: '[name="other_withholding"]'),
        ], actions: ['calculate', 'export'], selector: 'form[action*="backup-withholding-calculator"]', resultSelector: '[data-analytics-result="main"]')]);
    }
}
