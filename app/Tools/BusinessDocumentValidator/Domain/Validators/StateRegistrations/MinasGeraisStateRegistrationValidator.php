<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Domain\Validators\StateRegistrations;

final class MinasGeraisStateRegistrationValidator extends AbstractStateRegistrationValidator
{
    public function state(): string { return 'MG'; }
    public function label(): string { return 'Minas Gerais'; }

    public function validate(string $registration): bool
    {
        $ie = $this->digits($registration);
        if (strlen($ie) !== 13 || preg_match('/^(\d)\1+$/', $ie)) return false;

        $base = substr($ie, 0, 3).'0'.substr($ie, 3, 8);
        $sum = 0;
        foreach (str_split($base) as $i => $digit) {
            $product = ((int) $digit) * ($i % 2 === 0 ? 1 : 2);
            $sum += intdiv($product, 10) + ($product % 10);
        }
        $first = (10 - ($sum % 10)) % 10;
        if ($first !== (int) $ie[11]) return false;

        $second = $this->modulus11(substr($ie, 0, 12), [3, 2, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2]);
        return $second === (int) $ie[12];
    }

    public function format(string $registration): string
    {
        $ie = $this->digits($registration);
        return strlen($ie) === 13 ? substr($ie, 0, 3).'.'.substr($ie, 3, 3).'.'.substr($ie, 6, 3).'/'.substr($ie, 9, 4) : $ie;
    }
}
