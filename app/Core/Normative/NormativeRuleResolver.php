<?php

declare(strict_types=1);

namespace App\Core\Normative;

use App\Core\Dates\EffectiveRuleResolver;
use App\Core\Dates\Exceptions\OverlappingEffectiveRules;
use App\Core\Dates\ReferenceDate;
use App\Core\Normative\Contracts\NormativeRule;
use App\Core\Normative\Exceptions\DuplicateNormativeRuleVersion;
use App\Core\Normative\Exceptions\NormativeRuleNotFound;

final readonly class NormativeRuleResolver
{
    public function __construct(private EffectiveRuleResolver $effectiveRules = new EffectiveRuleResolver) {}

    /**
     * @param iterable<NormativeRule> $rules
     */
    public function resolveCurrent(iterable $rules, string $identifier, ReferenceDate $referenceDate): NormativeRule
    {
        $catalog = $this->validatedCatalog($rules);
        $matches = array_values(array_filter(
            $catalog,
            static fn (NormativeRule $rule): bool => $rule->normativeMetadata()->identifier === $identifier,
        ));

        if ($matches === []) {
            throw new NormativeRuleNotFound("Nenhuma regra normativa cadastrada para [{$identifier}].");
        }

        /** @var NormativeRule $resolved */
        $resolved = $this->effectiveRules->resolve($matches, $referenceDate);

        return $resolved;
    }

    /**
     * Resolve a versão exata registrada em um cálculo histórico e confirma que ela
     * era aplicável na data de referência original.
     *
     * @param iterable<NormativeRule> $rules
     */
    public function resolveHistorical(
        iterable $rules,
        string $identifier,
        NormativeRuleVersion $version,
        ReferenceDate $referenceDate,
    ): NormativeRule {
        foreach ($this->validatedCatalog($rules) as $rule) {
            $metadata = $rule->normativeMetadata();

            if ($metadata->identifier !== $identifier || ! $metadata->version->equals($version)) {
                continue;
            }

            if (! $metadata->effectivePeriod->contains($referenceDate)) {
                throw new NormativeRuleNotFound(
                    "A regra [{$identifier}] na versão [{$version}] não era vigente em {$referenceDate->toString()}.",
                );
            }

            return $rule;
        }

        throw new NormativeRuleNotFound("A regra [{$identifier}] na versão [{$version}] não foi encontrada.");
    }

    /**
     * @param iterable<NormativeRule> $rules
     * @return list<NormativeRule>
     */
    public function validatedCatalog(iterable $rules): array
    {
        $catalog = is_array($rules) ? array_values($rules) : iterator_to_array($rules, false);
        $seenVersions = [];
        $rulesByIdentifier = [];

        foreach ($catalog as $rule) {
            $metadata = $rule->normativeMetadata();
            $identity = $metadata->identifier.'@'.$metadata->version->value;

            if (isset($seenVersions[$identity])) {
                throw new DuplicateNormativeRuleVersion("A versão normativa [{$identity}] está duplicada.");
            }

            $seenVersions[$identity] = true;
            $rulesByIdentifier[$metadata->identifier][] = $rule;
        }

        foreach ($rulesByIdentifier as $identifier => $identifierRules) {
            try {
                $this->effectiveRules->assertNoOverlaps($identifierRules);
            } catch (OverlappingEffectiveRules $exception) {
                throw new OverlappingEffectiveRules(
                    "A regra normativa [{$identifier}] possui períodos de vigência sobrepostos.",
                    previous: $exception,
                );
            }
        }

        return $catalog;
    }
}
