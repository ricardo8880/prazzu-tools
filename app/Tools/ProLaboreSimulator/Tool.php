<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreSimulator;

use App\Core\Tools\Contracts\HasViews;
use App\Core\Tools\Contracts\HasWebRoutes;
use App\Core\Tools\Contracts\ToolModule;
use App\Core\Tools\Data\ToolFeature;
use App\Core\Tools\Data\ToolManifest;
use App\Core\Tools\Enums\ToolAccess;
use App\Core\Tools\Enums\ToolCategory;
use App\Core\Tools\Enums\ToolFeatureTier;
use App\Core\Tools\Enums\ToolStatus;

final class Tool implements HasViews, HasWebRoutes, ToolModule
{
    public const SLUG = 'simulador-pro-labore-ideal';

    public function manifest(): ToolManifest
    {
        return new ToolManifest(
            slug: self::SLUG,
            name: 'Simulador de Pró-Labore Ideal',
            description: 'Simule pró-labore, INSS, IRRF, valor líquido e custo empresarial com memória transparente.',
            category: ToolCategory::Fiscal,
            icon: 'bi-person-badge',
            routeName: 'tools.simulador-pro-labore-ideal.index',
            version: '1.0.0',
            access: ToolAccess::Free,
            status: ToolStatus::Beta,
            position: 30,
            featured: true,
            keywords: ['pró-labore', 'inss pró-labore', 'irrf pró-labore', 'retirada de sócio'],
            features: [
                new ToolFeature('calculate', 'Cálculo completo do pró-labore', ToolFeatureTier::Essential),
                new ToolFeature('memory', 'Memória e regras normativas', ToolFeatureTier::Essential),
                new ToolFeature('scenarios', 'Cenários anuais e comparação entre sócios', ToolFeatureTier::Plus),
            ],
        );
    }

    public function webRoutesPath(): string { return __DIR__.'/Routes/web.php'; }
    public function viewsPath(): string { return __DIR__.'/Resources/views'; }
    public function viewsNamespace(): string { return 'tools-simulador-pro-labore-ideal'; }
}
