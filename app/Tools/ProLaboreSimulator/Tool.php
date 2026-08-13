<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreSimulator;

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
    public const SLUG = 'simulador-pro-labore-ideal';

    public function analyticsJourney(): ToolAnalyticsJourney
    {
        return new ToolAnalyticsJourney(
            toolSlug: 'simulador-pro-labore-ideal',
            forms: [
                new ToolAnalyticsForm(
                    key: 'main',
                    steps: ['input'],
                    fields: [
                        new ToolAnalyticsField('competence', 'input', selector: '[name="competence"]'),
                        new ToolAnalyticsField('company_regime', 'input', selector: '[name="company_regime"]'),
                        new ToolAnalyticsField('gross_pro_labore', 'input', selector: '[name="gross_pro_labore"]'),
                        new ToolAnalyticsField('dependents', 'input', selector: '[name="dependents"]'),
                        new ToolAnalyticsField('other_official_social_security', 'input', selector: '[name="other_official_social_security"]'),
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
            name: 'Simulador de Pró-Labore Ideal',
            description: 'Simule pró-labore, INSS, IRRF, valor líquido e custo empresarial com memória transparente.',
            category: ToolCategory::Fiscal,
            icon: 'bi-person-badge',
            routeName: 'tools.simulador-pro-labore-ideal.index',
            vertical: 'contabilidade',
            version: '1.1.0',
            access: ToolAccess::Free,
            status: ToolStatus::Beta,
            position: 30,
            featured: true,
            supportsHistory: true,
            keywords: ['pró-labore', 'inss pró-labore', 'irrf pró-labore', 'retirada de sócio'],
            capabilities: [
                ToolCapability::History,
                ToolCapability::VersionedPersistence,
                ToolCapability::Export,
            ],
            features: [
                new ToolFeature('calculate', 'Cálculo completo do pró-labore', ToolFeatureTier::Essential),
                new ToolFeature('memory', 'Memória e regras normativas', ToolFeatureTier::Essential),
                new ToolFeature('scenarios', 'Comparação anual de múltiplos valores de pró-labore', ToolFeatureTier::Plus),
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
        return 'tools-simulador-pro-labore-ideal';
    }
}
