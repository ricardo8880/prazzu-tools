<?php

declare(strict_types=1);

namespace App\Tools\ContractGenerator;

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

final class Tool implements HasHistoryPolicy, HasWebRoutes, HasViews, ToolModule
{
    public const SLUG = 'gerador-de-contratos';

    public function manifest(): ToolManifest
    {
        return new ToolManifest(
            slug: self::SLUG,
            name: 'Gerador de Contratos',
            description: 'Gere contratos completos a partir de perguntas guiadas, revise o texto e prepare a exportação em PDF ou Word.',
            category: ToolCategory::Generators,
            icon: 'bi-file-earmark-text',
            routeName: 'tools.gerador-de-contratos.index',
            version: '0.5.0',
            access: ToolAccess::Free,
            status: ToolStatus::Beta,
            supportsHistory: true,
            storesSensitiveData: true,
            keywords: ['contrato', 'gerador de contrato', 'contrato online', 'modelo de contrato', 'documento contratual'],
            capabilities: [
                ToolCapability::History,
                ToolCapability::VersionedPersistence,
                ToolCapability::Export,
                ToolCapability::SensitiveData,
            ],
            features: [
                new ToolFeature('guided_generation', 'Geração completa a partir de perguntas', ToolFeatureTier::Essential),
                new ToolFeature('contract_editor', 'Edição do texto antes da exportação', ToolFeatureTier::Essential),
                new ToolFeature('pdf_export', 'Exportação do contrato em PDF', ToolFeatureTier::Essential),
                new ToolFeature('word_export', 'Exportação do contrato em Word', ToolFeatureTier::Essential),
                new ToolFeature('contract_library', 'Biblioteca ampliada de contratos', ToolFeatureTier::Plus),
                new ToolFeature('smart_clauses', 'Cláusulas inteligentes', ToolFeatureTier::Plus),
                new ToolFeature('favorites', 'Contratos favoritos', ToolFeatureTier::Plus),
                new ToolFeature('company_autofill', 'Preenchimento automático da empresa', ToolFeatureTier::Plus),
                new ToolFeature('history', 'Histórico de contratos', ToolFeatureTier::Plus),
                new ToolFeature('version_comparison', 'Comparação entre versões', ToolFeatureTier::Plus),
            ],
            persistence: new ToolPersistencePolicy(enabled: true, schemaVersion: 1, retentionDays: 365, minimumReadableSchemaVersion: 1),
            export: new ToolExportPolicy(enabled: true, formats: ['pdf', 'docx', 'json']),
            sharing: ToolSharingPolicy::disabled(),
            sensitiveData: new ToolSensitiveDataPolicy(SensitiveDataMode::Encrypted, [
                'first_party_name',
                'first_party_document',
                'first_party_address',
                'second_party_name',
                'second_party_document',
                'second_party_address',
                'contract_text',
            ]),
        );
    }

    public function historyPolicy(): ToolHistoryPolicy
    {
        return new ToolHistoryPolicy(
            enabled: true,
            retentionDays: 365,
            inputFields: [
                'contract_type',
                'first_party_name',
                'first_party_document_type',
                'first_party_document',
                'first_party_address',
                'first_party_city',
                'first_party_state',
                'second_party_name',
                'second_party_document_type',
                'second_party_document',
                'second_party_address',
                'second_party_city',
                'second_party_state',
                'amount',
                'payment_terms',
                'service_description',
                'start_date',
                'end_date',
                'termination_notice_days',
                'asset_description',
                'delivery_date',
                'delivery_location',
                'jurisdiction_city',
                'jurisdiction_state',
                'signing_city',
                'signing_date',
                'additional_terms',
            ],
            resultFields: ['contract_text'],
            sensitiveFields: [
                'first_party_name',
                'first_party_document',
                'first_party_address',
                'second_party_name',
                'second_party_document',
                'second_party_address',
                'contract_text',
            ],
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
        return 'tools-gerador-de-contratos';
    }
}
