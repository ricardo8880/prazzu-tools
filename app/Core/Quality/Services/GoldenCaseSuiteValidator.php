<?php

declare(strict_types=1);

namespace App\Core\Quality\Services;

use App\Core\Exceptions\InvalidValue;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Data\ToolQualityRequirements;

final class GoldenCaseSuiteValidator
{
    public function validate(GoldenCaseSuite $suite, ToolQualityRequirements $requirements): void
    {
        foreach ($requirements->requiredGoldenCaseKinds as $kind) {
            if (! $suite->hasKind($kind)) {
                throw new InvalidValue("A suíte [{$suite->toolSlug}] não possui um caso dourado obrigatório do tipo [{$kind->value}].");
            }
        }
    }
}
