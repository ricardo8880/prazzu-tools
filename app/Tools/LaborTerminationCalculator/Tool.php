<?php

declare(strict_types=1);

namespace App\Tools\LaborTerminationCalculator;

use App\Core\Tools\Contracts\HasViews;
use App\Core\Tools\Contracts\HasWebRoutes;
use App\Core\Tools\Contracts\ToolModule;
use App\Core\Tools\Data\ToolFeature;
use App\Core\Tools\Data\ToolManifest;
use App\Core\Tools\Enums\ToolAccess;
use App\Core\Tools\Enums\ToolCategory;
use App\Core\Tools\Enums\ToolFeatureTier;
use App\Core\Tools\Enums\ToolStatus;
use App\Core\Tools\History\Contracts\HasHistoryPolicy;
use App\Core\Tools\History\Data\ToolHistoryPolicy;

final class Tool implements ToolModule, HasWebRoutes, HasViews, HasHistoryPolicy
{
    /** @var array<int, string> */
    private const HISTORY_INPUT_FIELDS = [
        'monthly_salary', 'admission_date', 'termination_date', 'termination_type', 'contract_type', 'notice_type',
        'days_worked_in_month', 'overdue_vacation_periods', 'double_vacation_periods', 'fgts_balance',
        'domestic_indemnity_reserve_balance', 'other_discounts', 'dependents', 'commission_average',
        'overtime_average', 'recurring_additions', 'contract_end_date', 'early_termination_initiative',
        'article_480_discount', 'extraordinary_indemnities',
    ];

    /** @var array<int, string> */
    private const HISTORY_RESULT_FIELDS = [
        'salary_base', 'salary_balance', 'overdue_vacation', 'overdue_vacation_third', 'proportional_vacation',
        'proportional_vacation_third', 'proportional_thirteenth_salary', 'notice_pay', 'article_479_indemnity',
        'extraordinary_indemnities', 'gross_total', 'inss_salary', 'inss_thirteenth', 'irrf_salary',
        'irrf_thirteenth', 'notice_discount', 'article_480_discount', 'other_discounts', 'total_discounts',
        'net_total', 'fgts_balance', 'domestic_indemnity_reserve_balance', 'domestic_compensatory_deposit',
        'fgts_termination_deposit', 'fgts_penalty', 'estimated_fgts_available', 'fgts_withdrawal_percentage',
        'notice_days', 'projected_termination_date', 'termination_type_label', 'notice_type_label', 'is_domestic',
        'warnings', 'rule_version', 'tax_table_version',
    ];

    public function manifest(): ToolManifest
    {
        return new ToolManifest(
            slug: 'calculadora-de-rescisao',
            name: 'Calculadora de Rescisão Trabalhista',
            description: 'Estime saldo de salário, férias, 13º, aviso-prévio, FGTS e multa rescisória.',
            category: ToolCategory::Labor,
            icon: 'bi-briefcase',
            routeName: 'tools.calculadora-de-rescisao.index',
            version: '1.0.0',
            access: ToolAccess::Free,
            status: ToolStatus::Active,
            position: 10,
            featured: true,
            supportsHistory: true,
            storesSensitiveData: true,
            keywords: ['rescisão', 'trabalhista', 'saldo de salário', 'férias', '13º salário', 'aviso-prévio', 'fgts', 'contrato de experiência', 'artigo 479', 'férias em dobro', 'comissões', 'empregado doméstico', 'simples doméstico', 'indenização compensatória', 'histórico', 'pdf', 'relatório'],
            features: [
                new ToolFeature('calculate', 'Cálculo completo da rescisão', ToolFeatureTier::Essential),
                new ToolFeature('current_report', 'Relatório completo do cálculo atual', ToolFeatureTier::Essential),
                new ToolFeature('history', 'Histórico de cálculos', ToolFeatureTier::Plus),
                new ToolFeature('repeat_history', 'Repetição de cálculo salvo', ToolFeatureTier::Plus),
                new ToolFeature('historical_report', 'Relatório de cálculo salvo', ToolFeatureTier::Plus),
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
        return 'tools-calculadora-de-rescisao';
    }

    public function historyPolicy(): ToolHistoryPolicy
    {
        return new ToolHistoryPolicy(
            enabled: true,
            retentionDays: 180,
            inputFields: self::HISTORY_INPUT_FIELDS,
            resultFields: self::HISTORY_RESULT_FIELDS,
            sensitiveFields: self::HISTORY_INPUT_FIELDS,
        );
    }
}
