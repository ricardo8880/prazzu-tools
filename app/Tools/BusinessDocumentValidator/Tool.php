<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator;

use App\Core\Tools\Contracts\HasServiceProviders;
use App\Core\Tools\Contracts\HasViews;
use App\Core\Tools\Contracts\HasWebRoutes;
use App\Core\Tools\Contracts\HasToolIntegrations;
use App\Core\Tools\Contracts\ToolModule;
use App\Core\ToolIntegration\Data\ToolIntegrationManifest;
use App\Core\Tools\Data\ToolFeature;
use App\Core\Tools\Data\ToolManifest;
use App\Core\Tools\Enums\ToolAccess;
use App\Core\Tools\Enums\ToolCategory;
use App\Core\Tools\Enums\ToolFeatureTier;
use App\Core\Tools\Enums\ToolStatus;
use App\Core\Tools\History\Contracts\HasHistoryPolicy;
use App\Core\Tools\History\Data\ToolHistoryPolicy;
use App\Tools\BusinessDocumentValidator\Infrastructure\Providers\BusinessDocumentValidatorServiceProvider;

final class Tool implements HasToolIntegrations, HasHistoryPolicy, HasServiceProviders, HasViews, HasWebRoutes, ToolModule
{
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
            slug: 'validador-de-cnpj',
            name: 'Validador Inteligente de CNPJ, CPF e IE',
            description: 'Valide documentos empresariais e pessoais com uma experiência preparada para consultas e análises de inconsistências.',
            category: ToolCategory::Validators,
            icon: 'bi-shield-check',
            routeName: 'tools.validador-de-cnpj.index',
            version: '1.0.0',
            access: ToolAccess::Free,
            status: ToolStatus::Active,
            position: 30,
            featured: true,
            supportsHistory: true,
            storesSensitiveData: true,
            keywords: [
                'cnpj',
                'cpf',
                'inscrição estadual',
                'ie',
                'empresa',
                'validação',
                'consulta cadastral',
            ],
            features: [
                new ToolFeature('validate_document', 'Validação individual de CPF e CNPJ', ToolFeatureTier::Essential),
                new ToolFeature('validate_state_registration', 'Validação individual de Inscrição Estadual', ToolFeatureTier::Essential),
                new ToolFeature('lookup_company', 'Consulta cadastral individual de CNPJ', ToolFeatureTier::Essential),
                new ToolFeature('analyze_consistency', 'Análise individual de inconsistências', ToolFeatureTier::Essential),
                new ToolFeature('batch_processing', 'Validação e consulta em lote', ToolFeatureTier::Plus),
                new ToolFeature('batch_export', 'Exportação e relatório do lote', ToolFeatureTier::Plus),
                new ToolFeature('history', 'Histórico de validações', ToolFeatureTier::Plus),
            ],
        );
    }

    public function historyPolicy(): ToolHistoryPolicy
    {
        return new ToolHistoryPolicy(
            enabled: true,
            retentionDays: 90,
            inputFields: ['file_name', 'format', 'total_rows', 'consult_registry'],
            resultFields: [
                'summary.total', 'summary.valid', 'summary.invalid', 'summary.duplicates',
                'summary.with_inconsistencies', 'summary.registry_consulted', 'summary.registry_unavailable',
            ],
            sensitiveFields: ['file_name'],
        );
    }

    public function serviceProviders(): array
    {
        return [BusinessDocumentValidatorServiceProvider::class];
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
        return 'tools-validador-de-cnpj';
    }
}
