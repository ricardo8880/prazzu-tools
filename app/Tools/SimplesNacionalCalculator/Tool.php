<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator;

use App\Core\Tools\Contracts\HasMigrations;
use App\Core\Tools\Contracts\HasViews;
use App\Core\Tools\Contracts\HasWebRoutes;
use App\Core\Tools\Contracts\ToolModule;
use App\Core\Tools\Data\ToolManifest;
use App\Core\Tools\Enums\ToolAccess;
use App\Core\Tools\Enums\ToolCategory;
use App\Core\Tools\Enums\ToolStatus;

final class Tool implements HasMigrations, HasViews, HasWebRoutes, ToolModule
{
    public function manifest(): ToolManifest
    {
        return new ToolManifest(
            slug: 'calculadora-simples-nacional',
            name: 'Calculadora de Simples Nacional',
            description: 'Estime o DAS, identifique faixa, anexo e alíquota efetiva, incluindo análise do Fator R.',
            category: ToolCategory::Fiscal,
            icon: 'bi-calculator',
            routeName: 'tools.calculadora-simples-nacional.index',
            version: '1.0.0',
            access: ToolAccess::Free,
            status: ToolStatus::Beta,
            position: 10,
            featured: true,
            supportsHistory: false,
            storesSensitiveData: false,
            keywords: ['simples nacional', 'das', 'fator r', 'anexo', 'alíquota efetiva'],
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
        return 'tools-calculadora-simples-nacional';
    }

    public function migrationsPath(): string
    {
        return __DIR__.'/Infrastructure/Database/Migrations';
    }
}
