<?php

declare(strict_types=1);

namespace App\Tools\ReceiptIssuer\Domain\Services;

use App\Core\Money\BrazilianMoneyInWords as CoreBrazilianMoneyInWords;
use App\Core\Money\Money;

/**
 * @deprecated Use App\Core\Money\BrazilianMoneyInWords directly.
 */
final readonly class BrazilianMoneyInWords
{
    public function __construct(private CoreBrazilianMoneyInWords $converter = new CoreBrazilianMoneyInWords()) {}

    public function convert(Money $money): string
    {
        return $this->converter->convert($money);
    }
}
