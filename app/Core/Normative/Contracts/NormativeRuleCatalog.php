<?php

declare(strict_types=1);

namespace App\Core\Normative\Contracts;

use App\Core\Dates\ReferenceDate;
use App\Core\Normative\NormativeRuleVersion;

interface NormativeRuleCatalog
{
    public function current(string $identifier, ReferenceDate $referenceDate): NormativeRule;

    public function historical(string $identifier, NormativeRuleVersion $version, ReferenceDate $referenceDate): NormativeRule;

    /** @return list<NormativeRule> */
    public function all(): array;
}
