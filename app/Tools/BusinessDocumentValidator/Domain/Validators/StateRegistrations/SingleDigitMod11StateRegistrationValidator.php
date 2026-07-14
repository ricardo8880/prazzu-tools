<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Domain\Validators\StateRegistrations;

final class SingleDigitMod11StateRegistrationValidator extends AbstractStateRegistrationValidator
{
    /** @param list<int> $weights */
    public function __construct(
        private readonly string $stateCode,
        private readonly string $stateName,
        private readonly int $length,
        private readonly array $weights,
        private readonly ?string $requiredPrefix = null,
    ) {}

    public function state(): string { return $this->stateCode; }
    public function label(): string { return $this->stateName; }

    public function validate(string $registration): bool
    {
        $ie = $this->digits($registration);
        if (strlen($ie) !== $this->length || preg_match('/^(\d)\1+$/', $ie)) return false;
        if ($this->requiredPrefix !== null && ! str_starts_with($ie, $this->requiredPrefix)) return false;

        return $this->modulus11(substr($ie, 0, -1), $this->weights) === (int) substr($ie, -1);
    }
}
