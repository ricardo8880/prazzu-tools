<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Domain\Validators\StateRegistrations;

final class ParanaStateRegistrationValidator extends AbstractStateRegistrationValidator
{
    public function state(): string
    {
        return 'PR';
    }

    public function label(): string
    {
        return 'Paraná';
    }

    public function validate(string $registration): bool
    {
        $ie = $this->digits($registration);
        if (strlen($ie) !== 10 || preg_match('/^(\d)\1+$/', $ie)) {
            return false;
        }
        $first = $this->modulus11(substr($ie, 0, 8), [3, 2, 7, 6, 5, 4, 3, 2]);
        if ($first !== (int) $ie[8]) {
            return false;
        }
        $second = $this->modulus11(substr($ie, 0, 9), [4, 3, 2, 7, 6, 5, 4, 3, 2]);

        return $second === (int) $ie[9];
    }
}
