<?php

declare(strict_types=1);

namespace App\Tools\LaborTerminationCalculator\Domain\Enums;

enum NoticeType: string
{
    case Worked = 'worked';
    case Indemnified = 'indemnified';
    case NotWorked = 'not_worked';
    case NotApplicable = 'not_applicable';

    public function label(): string
    {
        return match ($this) {
            self::Worked => 'Trabalhado',
            self::Indemnified => 'Indenizado',
            self::NotWorked => 'Não cumprido / passível de desconto',
            self::NotApplicable => 'Não se aplica',
        };
    }
}
