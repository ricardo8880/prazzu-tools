<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Domain\Data;

use App\Core\Money\Money;
use DateTimeImmutable;

final readonly class CommercialProposal
{
    /** @param list<string> $services */
    public function __construct(
        public string $clientCompany,
        public ?string $clientDocument,
        public string $contactName,
        public string $accountingFirm,
        public Money $monthlyFee,
        public Money $setupFee,
        public int $dueDay,
        public int $validityDays,
        public array $services,
        public ?string $notes,
        public DateTimeImmutable $issuedAt,
        public DateTimeImmutable $validUntil,
    ) {}
}
