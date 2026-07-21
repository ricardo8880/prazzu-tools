<?php

declare(strict_types=1);

namespace App\Tools\FiscalXmlConverter;

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

final class Tool implements HasHistoryPolicy, HasToolIntegrations, HasViews, HasWebRoutes, ToolModule
{
    public const SLUG = 'conversor-fiscal-xml';

    public function integrations(): ToolIntegrationManifest
    {
        return new ToolIntegrationManifest(publishes: [], accepts: []);
    }

    public function manifest(): ToolManifest
    {
        return new ToolManifest(
            slug: self::SLUG,
            name: 'Conversor Fiscal de XML',
            description: 'Extraia dados estruturados de NF-e e NFC-e com produtos, NCM, CFOP, impostos, totais e alertas de consistência.',
            category: ToolCategory::Fiscal,
            icon: 'bi-file-earmark-code',
            routeName: 'tools.conversor-fiscal-xml.index',
            version: '1.0.0',
            access: ToolAccess::Free,
            status: ToolStatus::Active,
            position: 40,
            featured: false,
            supportsHistory: true,
            storesSensitiveData: true,
            keywords: ['xml fiscal', 'nfe', 'nf-e', 'nfce', 'nfc-e', 'ncm', 'cfop', 'impostos da nota'],
            capabilities: [ToolCapability::Export, ToolCapability::History, ToolCapability::SensitiveData, ToolCapability::VersionedPersistence],
            features: [
                new ToolFeature('single_xml_reading', 'Leitura individual de NF-e e NFC-e', ToolFeatureTier::Essential),
                new ToolFeature('fiscal_data_extraction', 'Extração de emitente, destinatário, produtos, NCM, CFOP, impostos e totais', ToolFeatureTier::Essential),
                new ToolFeature('consistency_warnings', 'Alertas de estrutura e consistência do documento', ToolFeatureTier::Essential),
                new ToolFeature('batch_processing', 'Processamento de múltiplos XMLs', ToolFeatureTier::Plus),
                new ToolFeature('professional_export', 'Exportações consolidadas em CSV, JSON e XLSX', ToolFeatureTier::Plus),
                new ToolFeature('document_comparison', 'Comparação entre documentos e períodos', ToolFeatureTier::Plus),
                new ToolFeature('history', 'Histórico de processamentos para usuários autenticados', ToolFeatureTier::Plus),
            ],
            persistence: new ToolPersistencePolicy(enabled: true, schemaVersion: 1, retentionDays: 365, minimumReadableSchemaVersion: 1),
            export: new ToolExportPolicy(enabled: true, formats: ['csv', 'json', 'xlsx']),
            sharing: ToolSharingPolicy::disabled(),
            sensitiveData: new ToolSensitiveDataPolicy(SensitiveDataMode::Encrypted, ['history_payload']),
        );
    }


    public function historyPolicy(): ToolHistoryPolicy
    {
        return new ToolHistoryPolicy(
            enabled: true,
            retentionDays: 365,
            inputFields: ['mode', 'model', 'access_key', 'number', 'series', 'received'],
            resultFields: ['model', 'access_key', 'number', 'series', 'issued_at', 'issuer', 'recipient', 'totals', 'items', 'warnings', 'documents', 'errors', 'summary'],
            sensitiveFields: ['access_key', 'issuer', 'recipient', 'documents'],
        );
    }

    public function webRoutesPath(): string { return __DIR__.'/Routes/web.php'; }
    public function viewsPath(): string { return __DIR__.'/Resources/views'; }
    public function viewsNamespace(): string { return 'tools-conversor-fiscal-xml'; }
}
