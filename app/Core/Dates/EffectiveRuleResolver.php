<?php

declare(strict_types=1);

namespace App\Core\Dates;

use App\Core\Dates\Contracts\EffectiveDated;
use App\Core\Dates\Exceptions\NoEffectiveRule;
use App\Core\Dates\Exceptions\OverlappingEffectiveRules;

/**
 * @template TRule of EffectiveDated
 */
final class EffectiveRuleResolver
{
    /**
     * @param iterable<TRule> $rules
     * @return TRule
     */
    public function resolve(iterable $rules, ReferenceDate $date): EffectiveDated
    {
        $matches = [];

        foreach ($rules as $rule) {
            if ($rule->effectivePeriod()->contains($date)) {
                $matches[] = $rule;
            }
        }

        if ($matches === []) {
            throw new NoEffectiveRule("Nenhuma regra vigente encontrada para {$date->toString()}.");
        }

        if (count($matches) > 1) {
            throw new OverlappingEffectiveRules("Mais de uma regra vigente encontrada para {$date->toString()}.");
        }

        return $matches[0];
    }

    /**
     * @param iterable<TRule> $rules
     */
    public function assertNoOverlaps(iterable $rules): void
    {
        $list = is_array($rules) ? array_values($rules) : iterator_to_array($rules, false);

        foreach ($list as $leftIndex => $left) {
            foreach (array_slice($list, $leftIndex + 1) as $right) {
                if ($left->effectivePeriod()->overlaps($right->effectivePeriod())) {
                    throw new OverlappingEffectiveRules('Foram encontradas regras com períodos de vigência sobrepostos.');
                }
            }
        }
    }
}
