<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Application\Features;

enum SimplesNacionalFeature: string
{
    case Calculate = 'calculate';
    case FactorR = 'factor_r';
    case EffectiveRate = 'effective_rate';
    case EstimatedDas = 'estimated_das';
    case CompareScenarios = 'compare_scenarios';
    case CompareAnnexes = 'compare_annexes';
    case MonthlyHistory = 'monthly_history';
    case AnnualProjection = 'annual_projection';
    case Alerts = 'alerts';

    public function label(): string
    {
        return match ($this) {
            self::Calculate => 'Cálculo por anexo e faixa',
            self::FactorR => 'Cálculo do Fator R',
            self::EffectiveRate => 'Alíquota efetiva',
            self::EstimatedDas => 'DAS estimado',
            self::CompareScenarios => 'Simulações de cenários',
            self::CompareAnnexes => 'Comparação entre anexos',
            self::MonthlyHistory => 'Histórico mensal',
            self::AnnualProjection => 'Projeção anual',
            self::Alerts => 'Alertas tributários',
        };
    }
}
