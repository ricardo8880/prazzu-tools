<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Domain\Enums;

enum EstimateStatus: string
{
    case Available = 'available';
    case InsufficientData = 'insufficient_data';
    case UnsupportedScenario = 'unsupported_scenario';
    case NormativeGap = 'normative_gap';

    public function isComparable(): bool
    {
        return $this === self::Available;
    }

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Disponível para comparação',
            self::InsufficientData => 'Dados insuficientes',
            self::UnsupportedScenario => 'Cenário ainda não suportado',
            self::NormativeGap => 'Regra normativa indisponível',
        };
    }
}
