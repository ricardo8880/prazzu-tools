<?php

declare(strict_types=1);

namespace App\Core\Normative;

use App\Core\Dates\ReferenceDate;
use App\Core\Normative\Contracts\NormativeRule;

final readonly class NormativeRuleSnapshot
{
    /** @param list<array<string, mixed>> $references */
    public function __construct(
        public string $identifier,
        public string $version,
        public string $referenceDate,
        public string $effectiveFrom,
        public ?string $effectiveUntil,
        public string $verifiedAt,
        public string $verifiedBy,
        public array $references,
    ) {}

    public static function fromRule(NormativeRule $rule, ReferenceDate $referenceDate): self
    {
        $metadata = $rule->normativeMetadata();

        return new self(
            $metadata->identifier,
            $metadata->version->value,
            $referenceDate->toString(),
            $metadata->effectivePeriod->startsAt->toString(),
            $metadata->effectivePeriod->endsAt?->toString(),
            $metadata->verifiedAt->toString(),
            $metadata->verifiedBy,
            array_map(static fn (NormativeReference $reference): array => $reference->toArray(), $metadata->references),
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'identifier' => $this->identifier,
            'version' => $this->version,
            'reference_date' => $this->referenceDate,
            'effective_from' => $this->effectiveFrom,
            'effective_until' => $this->effectiveUntil,
            'verified_at' => $this->verifiedAt,
            'verified_by' => $this->verifiedBy,
            'references' => $this->references,
        ];
    }
}
