<?php

declare(strict_types=1);

namespace App\Core\Normative;

enum NormativeSourceType: string
{
    case Constitution = 'constitution';
    case ComplementaryLaw = 'complementary_law';
    case Law = 'law';
    case Decree = 'decree';
    case ProvisionalMeasure = 'provisional_measure';
    case NormativeInstruction = 'normative_instruction';
    case Ordinance = 'ordinance';
    case Resolution = 'resolution';
    case OfficialTable = 'official_table';
    case CourtDecision = 'court_decision';
    case Other = 'other';
}
