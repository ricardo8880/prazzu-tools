<?php

declare(strict_types=1);

namespace App\Core\Tools\Calculation\Data;

use App\Core\Normative\NormativeRuleSnapshot;
use InvalidArgumentException;

final readonly class CalculationMemory
{
    /**
     * @param list<CalculationMemoryStep> $steps
     * @param list<NormativeRuleSnapshot> $normativeRules
     * @param list<string> $assumptions
     */
    public function __construct(
        public string $schemaVersion,
        public array $steps,
        public array $normativeRules = [],
        public array $assumptions = [],
        public bool $isEstimate = false,
    ) {
        if (! preg_match('/^\d+\.\d+\.\d+$/', $schemaVersion)) {
            throw new InvalidArgumentException('A memória de cálculo precisa de versão semântica.');
        }
        foreach ($steps as $step) {
            if (! $step instanceof CalculationMemoryStep) {
                throw new InvalidArgumentException('A memória contém uma etapa inválida.');
            }
        }
        foreach ($normativeRules as $rule) {
            if (! $rule instanceof NormativeRuleSnapshot) {
                throw new InvalidArgumentException('A memória contém uma referência normativa inválida.');
            }
        }
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'schema_version' => $this->schemaVersion,
            'is_estimate' => $this->isEstimate,
            'assumptions' => $this->assumptions,
            'steps' => array_map(static fn (CalculationMemoryStep $step): array => $step->toArray(), $this->steps),
            'normative_rules' => array_map(static fn (NormativeRuleSnapshot $rule): array => $rule->toArray(), $this->normativeRules),
        ];
    }
}
