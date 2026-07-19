<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Domain\Validators\StateRegistrations;

final class SaoPauloStateRegistrationValidator extends AbstractStateRegistrationValidator
{
    public function state(): string
    {
        return 'SP';
    }

    public function label(): string
    {
        return 'São Paulo';
    }

    public function validate(string $registration): bool
    {
        $ie = $this->digits($registration);
        if (strlen($ie) !== 12 || preg_match('/^(\d)\1+$/', $ie)) {
            return false;
        }

        $sum = 0;
        foreach ([1, 3, 4, 5, 6, 7, 8, 10] as $i => $weight) {
            $sum += ((int) $ie[$i]) * $weight;
        }
        $first = $sum % 11;
        if ($first === 10) {
            $first = 0;
        }
        if ($first !== (int) $ie[8]) {
            return false;
        }

        $sum = 0;
        foreach ([3, 2, 10, 9, 8, 7, 6, 5, 4, 3, 2] as $i => $weight) {
            $sum += ((int) $ie[$i]) * $weight;
        }
        $second = $sum % 11;
        if ($second === 10) {
            $second = 0;
        }

        return $second === (int) $ie[11];
    }

    public function format(string $registration): string
    {
        $ie = $this->digits($registration);

        return strlen($ie) === 12 ? substr($ie, 0, 3).'.'.substr($ie, 3, 3).'.'.substr($ie, 6, 3).'.'.substr($ie, 9, 3) : $ie;
    }
}
