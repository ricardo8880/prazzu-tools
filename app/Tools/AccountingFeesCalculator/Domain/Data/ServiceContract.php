<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Domain\Data;

use App\Core\Money\Money;
use DateTimeImmutable;

final readonly class ServiceContract
{
    /** @param list<string> $services */
    public function __construct(
        public string $clientCompany,
        public ?string $clientDocument,
        public string $clientRepresentative,
        public string $accountingFirm,
        public ?string $accountingFirmDocument,
        public string $accountingRepresentative,
        public Money $monthlyFee,
        public int $dueDay,
        public int $durationMonths,
        public string $adjustmentIndex,
        public int $lateFeePercent,
        public int $terminationNoticeDays,
        public array $services,
        public bool $includesLgpd,
        public bool $includesConfidentiality,
        public ?string $additionalTerms,
        public DateTimeImmutable $issuedAt,
        public DateTimeImmutable $startsAt,
        public DateTimeImmutable $endsAt,
    ) {}
}
