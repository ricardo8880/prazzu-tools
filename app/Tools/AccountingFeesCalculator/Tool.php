<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator;

use App\Core\Tools\Contracts\HasMigrations;
use App\Core\Tools\Contracts\HasViews;
use App\Core\Tools\Contracts\HasWebRoutes;
use App\Core\Tools\Contracts\ToolModule;
use App\Core\Tools\Data\ToolManifest;
use App\Core\Tools\Enums\ToolAccess;
use App\Core\Tools\Enums\ToolCategory;
use App\Core\Tools\Enums\ToolStatus;
use App\Core\Tools\History\Contracts\HasHistoryPolicy;
use App\Core\Tools\History\Data\ToolHistoryPolicy;

final class Tool implements HasHistoryPolicy, HasMigrations, HasViews, HasWebRoutes, ToolModule
{
    public function manifest(): ToolManifest
    {
        return new ToolManifest(
            slug: 'calculadora-de-honorarios-contabeis',
            name: 'Calculadora de Honorários Contábeis',
            description: 'Estime honorários contábeis com base no porte, regime tributário e complexidade da operação.',
            category: ToolCategory::Calculators,
            icon: 'bi-calculator',
            routeName: 'tools.calculadora-de-honorarios-contabeis.index',
            version: '1.1.0',
            access: ToolAccess::Free,
            status: ToolStatus::Active,
            position: 30,
            featured: true,
            supportsHistory: true,
            storesSensitiveData: false,
            keywords: ['honorários contábeis', 'precificação contábil', 'contador', 'mensalidade contábil', 'proposta comercial', 'contrato contábil'],
        );
    }

    public function historyPolicy(): ToolHistoryPolicy
    {
        return new ToolHistoryPolicy(
            enabled: true,
            retentionDays: 365,
            inputFields: ['monthly_revenue', 'employees', 'partners', 'monthly_invoices', 'monthly_bank_transactions', 'tax_regime', 'business_segment', 'complexity'],
            resultFields: ['minimum_fee', 'recommended_fee', 'upper_reference_fee', 'complexity_score', 'complexity_level', 'rule_version'],
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
        return 'tools-calculadora-de-honorarios-contabeis';
    }

    public function migrationsPath(): string
    {
        return __DIR__.'/Infrastructure/Database/Migrations';
    }
}
