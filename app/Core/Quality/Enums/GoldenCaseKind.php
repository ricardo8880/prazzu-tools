<?php

declare(strict_types=1);

namespace App\Core\Quality\Enums;

enum GoldenCaseKind: string
{
    case Typical = 'typical';
    case Boundary = 'boundary';
    case InvalidInput = 'invalid_input';
    case NonApplicable = 'non_applicable';
    case Rounding = 'rounding';
    case NormativeTransition = 'normative_transition';
    case Regression = 'regression';
}
