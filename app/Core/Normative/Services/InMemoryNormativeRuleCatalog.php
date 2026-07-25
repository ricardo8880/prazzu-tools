<?php

declare(strict_types=1);

namespace App\Core\Normative\Services;

use App\Core\Dates\ReferenceDate;
use App\Core\Normative\Contracts\NormativeRule;
use App\Core\Normative\Contracts\NormativeRuleCatalog;
use App\Core\Normative\NormativeRuleResolver;
use App\Core\Normative\NormativeRuleVersion;

final readonly class InMemoryNormativeRuleCatalog implements NormativeRuleCatalog
{
    /** @var list<NormativeRule> */
    private array $rules;

    /** @param iterable<NormativeRule> $rules */
    public function __construct(iterable $rules, private NormativeRuleResolver $resolver = new NormativeRuleResolver)
    {
        $this->rules = $this->resolver->validatedCatalog($rules);
    }

    public function current(string $identifier, ReferenceDate $referenceDate): NormativeRule
    {
        return $this->resolver->resolveCurrent($this->rules, $identifier, $referenceDate);
    }

    public function historical(string $identifier, NormativeRuleVersion $version, ReferenceDate $referenceDate): NormativeRule
    {
        return $this->resolver->resolveHistorical($this->rules, $identifier, $version, $referenceDate);
    }

    public function all(): array
    {
        return $this->rules;
    }
}
