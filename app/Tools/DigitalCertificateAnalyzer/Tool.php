<?php

declare(strict_types=1);

namespace App\Tools\DigitalCertificateAnalyzer;

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
use App\Core\Tools\Infrastructure\Enums\SensitiveDataMode;

final class Tool implements HasAnalyticsJourney, HasToolIntegrations, HasWebRoutes, HasViews, ToolModule
{
    public const SLUG = 'analisador-certificado-digital-a1';

    public function integrations(): ToolIntegrationManifest
    {
        return new ToolIntegrationManifest(publishes: [], accepts: []);
    }

    public function manifest(): ToolManifest
    {
        return new ToolManifest(
            slug: self::SLUG,
            name: 'Analisador de Certificado Digital A1',
            description: 'Leia um arquivo .pfx/.p12, confira titular, emissor e período de validade e gere um diagnóstico técnico sem armazenar o certificado ou a senha.',
            category: ToolCategory::Fiscal,
            icon: 'bi-shield-lock',
            routeName: 'tools.analisador-certificado-digital-a1.index',
            vertical: 'contabilidade',
            version: '1.0.0',
            access: ToolAccess::Free,
            status: ToolStatus::Active,
            position: 15,
            featured: true,
            supportsHistory: false,
            storesSensitiveData: true,
            keywords: ['certificado digital', 'A1', 'PFX', 'P12', 'e-CNPJ', 'e-CPF', 'validade', 'ICP-Brasil'],
            capabilities: [ToolCapability::Export, ToolCapability::SensitiveData],
            features: [
                new ToolFeature('analyze', 'Leitura e diagnóstico temporal do certificado A1', ToolFeatureTier::Essential),
                new ToolFeature('identity', 'Titular, emissor e CPF/CNPJ quando identificável', ToolFeatureTier::Essential),
                new ToolFeature('technical_report', 'Diagnóstico técnico e relatório PDF', ToolFeatureTier::Plus),
            ],
            persistence: ToolPersistencePolicy::disabled(),
            export: new ToolExportPolicy(true, ['pdf']),
            sharing: ToolSharingPolicy::disabled(),
            sensitiveData: new ToolSensitiveDataPolicy(SensitiveDataMode::Redacted, ['certificate_file', 'password']),
        );
    }


    public function analyticsJourney(): ToolAnalyticsJourney
    {
        return new ToolAnalyticsJourney(
            toolSlug: self::SLUG,
            forms: [
                new ToolAnalyticsForm(
                    key: 'main',
                    steps: ['upload'],
                    fields: [
                        new ToolAnalyticsField('certificate_file', 'upload', selector: '[name="certificate_file"]'),
                    ],
                    actions: ['analyze', 'export'],
                    selector: 'form[action*="analisador-certificado-digital-a1"]',
                    resultSelector: '[data-testid="tool-result"]',
                ),
            ],
        );
    }

    public function webRoutesPath(): string { return __DIR__.'/Routes/web.php'; }
    public function viewsPath(): string { return __DIR__.'/Resources/views'; }
    public function viewsNamespace(): string { return 'tools-analisador-certificado-digital-a1'; }
}
