<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator;

use App\Core\Tools\Contracts\HasViews;
use App\Core\Tools\Contracts\HasWebRoutes;
use App\Core\Tools\Contracts\ToolModule;
use App\Core\Tools\Data\ToolManifest;
use App\Core\Tools\Enums\ToolAccess;
use App\Core\Tools\Enums\ToolCategory;
use App\Core\Tools\Enums\ToolStatus;
use App\Core\Tools\History\Contracts\HasHistoryPolicy;
use App\Core\Tools\History\Data\ToolHistoryPolicy;

final class Tool implements ToolModule, HasWebRoutes, HasViews, HasHistoryPolicy
{
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
