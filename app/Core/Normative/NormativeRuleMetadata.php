<?php

declare(strict_types=1);

namespace App\Core\Normative;

use App\Core\Dates\EffectivePeriod;
use App\Core\Dates\ReferenceDate;
use App\Core\Exceptions\InvalidValue;

final readonly class NormativeRuleMetadata
{
    /**
     * @param list<NormativeReference> $references
     */
    public function __construct(
        public string $identifier,
        public NormativeRuleVersion $version,
        public EffectivePeriod $effectivePeriod,
        public array $references,
        public ReferenceDate $verifiedAt,
        public string $verifiedBy,
    ) {
        if (! preg_match('/^[a-z0-9]+(?:[._-][a-z0-9]+)*$/', $identifier)) {
            throw new InvalidValue('O identificador da regra normativa deve usar apenas letras minúsculas, números, ponto, hífen ou sublinhado.');
        }

        if (trim($verifiedBy) === '') {
            throw new InvalidValue('A regra normativa precisa identificar o responsável pela última verificação.');
        }

        if ($references === []) {
            throw new InvalidValue('A regra normativa precisa possuir ao menos uma referência oficial.');
        }

        foreach ($references as $reference) {
            if (! $reference instanceof NormativeReference) {
                throw new InvalidValue('A regra normativa possui uma referência inválida.');
            }

            if ($reference->officialUrl === null) {
                throw new InvalidValue('Toda referência de uma regra normativa precisa apontar para uma fonte oficial.');
            }

            if ($verifiedAt->isBefore($reference->publishedAt)) {
                throw new InvalidValue('A verificação normativa não pode ser anterior à publicação da fonte.');
            }
        }
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'identifier' => $this->identifier,
            'version' => $this->version->value,
            'effective_from' => $this->effectivePeriod->startsAt->toString(),
            'effective_until' => $this->effectivePeriod->endsAt?->toString(),
            'verified_at' => $this->verifiedAt->toString(),
            'verified_by' => $this->verifiedBy,
            'references' => array_map(
                static fn (NormativeReference $reference): array => $reference->toArray(),
                $this->references,
            ),
        ];
    }
}
