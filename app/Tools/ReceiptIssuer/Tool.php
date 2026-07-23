<?php

declare(strict_types=1);

namespace App\Tools\ReceiptIssuer;

use App\Core\ToolIntegration\Data\ToolIntegrationManifest;
use App\Core\Tools\Contracts\HasMigrations;
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

final class Tool implements HasHistoryPolicy, HasMigrations, HasToolIntegrations, HasWebRoutes, HasViews, ToolModule
{
    public const SLUG = 'emissor-de-recibos';

    public function integrations(): ToolIntegrationManifest
    {
        return new ToolIntegrationManifest(
            publishes: [],
            accepts: [],
        );
    }

    public function manifest(): ToolManifest
    {
        return new ToolManifest(
            slug: self::SLUG,
            name: 'Emissor de Recibos',
            description: 'Gere recibos completos, identificados e prontos para exportação, com validação de pagador, recebedor e valor por extenso.',
            category: ToolCategory::Documents,
            icon: 'bi-receipt',
            routeName: 'tools.emissor-de-recibos.index',
            version: '0.6.0',
            access: ToolAccess::Free,
            status: ToolStatus::Active,
            supportsHistory: true,
            storesSensitiveData: true,
            keywords: ['recibo', 'recibo de pagamento', 'recibo online', 'comprovante de pagamento', 'gerar recibo'],
            capabilities: [
                ToolCapability::History,
                ToolCapability::VersionedPersistence,
                ToolCapability::Export,
                ToolCapability::SensitiveData,
            ],
            features: [
                new ToolFeature('receipt_generation', 'Geração completa do recibo', ToolFeatureTier::Essential),
                new ToolFeature('party_validation', 'Validação de pagador e recebedor', ToolFeatureTier::Essential),
                new ToolFeature('amount_in_words', 'Valor monetário por extenso', ToolFeatureTier::Essential),
                new ToolFeature('pdf_export', 'Exportação do recibo em PDF', ToolFeatureTier::Essential),
                new ToolFeature('history', 'Histórico e reaproveitamento de recibos', ToolFeatureTier::Plus),
                new ToolFeature('saved_profiles', 'Perfis reutilizáveis de pagadores e recebedores', ToolFeatureTier::Plus),
                new ToolFeature('batch_generation', 'Geração de recibos em lote', ToolFeatureTier::Plus),
                new ToolFeature('custom_branding', 'Personalização visual do recibo', ToolFeatureTier::Plus),
            ],
            persistence: new ToolPersistencePolicy(enabled: true, schemaVersion: 1, retentionDays: 365, minimumReadableSchemaVersion: 1),
            export: new ToolExportPolicy(enabled: true, formats: ['pdf', 'json']),
            sharing: ToolSharingPolicy::disabled(),
            sensitiveData: new ToolSensitiveDataPolicy(SensitiveDataMode::Encrypted, ['payer_document', 'payee_document']),
        );
    }

    public function historyPolicy(): ToolHistoryPolicy
    {
        return new ToolHistoryPolicy(enabled: true, retentionDays: 365, inputFields: ['identifier', 'number', 'payer', 'payer_document', 'payee', 'payee_document', 'amount_minor', 'description', 'issued_at', 'city'], resultFields: ['receipt'], sensitiveFields: ['payer_document', 'payee_document']);
    }


    public function migrationsPath(): string
    {
        return __DIR__.'/Infrastructure/Database/Migrations';
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
        return 'tools-emissor-de-recibos';
    }
}
