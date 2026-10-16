<?php

declare(strict_types=1);

namespace App\Tools\ReceiptIssuer\Domain\Services;

use App\Core\Money\AmericanMoneyInWords as CoreAmericanMoneyInWords;
use App\Core\Money\Money;

/**
 * @deprecated Use App\Core\Money\AmericanMoneyInWords directly.
 */
final readonly class AmericanMoneyInWords
{
    public function __construct(private CoreAmericanMoneyInWords $converter = new CoreAmericanMoneyInWords) {}

    public function convert(Money $money): string
    {
        return $this->converter->convert($money);
    }
}
