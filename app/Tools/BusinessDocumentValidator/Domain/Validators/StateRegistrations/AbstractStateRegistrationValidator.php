<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Domain\Validators\StateRegistrations;

use App\Tools\BusinessDocumentValidator\Domain\Contracts\StateRegistrationValidator;

abstract class AbstractStateRegistrationValidator implements StateRegistrationValidator
{
    protected function digits(string $value): string
    {
        return preg_replace('/\D+/', '', $value) ?? '';
    }

    /** @param list<int> $weights */
    protected function modulus11(string $base, array $weights): int
    {
        $sum = 0;
        foreach ($weights as $index => $weight) {
            $sum += ((int) $base[$index]) * $weight;
        }

        $digit = 11 - ($sum % 11);

        return $digit >= 10 ? 0 : $digit;
    }

    public function format(string $registration): string
    {
        return $this->digits($registration);
    }
}
