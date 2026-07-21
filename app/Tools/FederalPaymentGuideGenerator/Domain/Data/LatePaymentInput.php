<?php

declare(strict_types=1);

namespace App\Tools\FederalPaymentGuideGenerator\Domain\Data;

use App\Core\Money\Money;
use DateTimeImmutable;

final readonly class LatePaymentInput
{
    public function __construct(
        public Money $principal,
        public DateTimeImmutable $dueDate,
        public DateTimeImmutable $paymentDate,
        public string $selicAccumulatedPercent,
    ) {
        if ($principal->minorAmount() < 0) {
            throw new \InvalidArgumentException('O principal não pode ser negativo.');
        }

        if ($paymentDate < $dueDate) {
            throw new \InvalidArgumentException('A data de pagamento não pode ser anterior ao vencimento.');
        }
    }
}
