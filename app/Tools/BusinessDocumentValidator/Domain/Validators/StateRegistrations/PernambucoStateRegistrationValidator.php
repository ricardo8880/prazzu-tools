<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Domain\Validators\StateRegistrations;

final class PernambucoStateRegistrationValidator extends AbstractStateRegistrationValidator
{
    public function state(): string
    {
        return 'PE';
    }

    public function label(): string
    {
        return 'Pernambuco';
    }

    public function validate(string $registration): bool
    {
        $ie = $this->digits($registration);
        if (strlen($ie) !== 9 || preg_match('/^(\d)\1+$/', $ie)) {
            return false;
        }
        $first = $this->modulus11(substr($ie, 0, 7), [8, 7, 6, 5, 4, 3, 2]);
        if ($first !== (int) $ie[7]) {
            return false;
        }
        $second = $this->modulus11(substr($ie, 0, 8), [9, 8, 7, 6, 5, 4, 3, 2]);

        return $second === (int) $ie[8];
    }
}
