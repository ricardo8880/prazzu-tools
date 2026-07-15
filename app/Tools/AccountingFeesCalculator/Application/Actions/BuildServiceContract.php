<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Application\Actions;

use App\Core\Money\Money;
use App\Tools\AccountingFeesCalculator\Domain\Data\ServiceContract;
use App\Tools\AccountingFeesCalculator\Domain\Enums\AccountingService;
use DateTimeImmutable;

final class BuildServiceContract
{
    /** @param array<string, mixed> $input */
    public function execute(array $input): ServiceContract
    {
        $startsAt = new DateTimeImmutable((string) $input['start_date']);
        $durationMonths = (int) $input['duration_months'];

        return new ServiceContract(
            clientCompany: trim((string) $input['client_company']),
            clientDocument: $this->nullableString($input['client_document'] ?? null),
            clientRepresentative: trim((string) $input['client_representative']),
            accountingFirm: trim((string) $input['accounting_firm']),
            accountingFirmDocument: $this->nullableString($input['accounting_firm_document'] ?? null),
            accountingRepresentative: trim((string) $input['accounting_representative']),
            monthlyFee: Money::fromDecimal((string) $input['monthly_fee']),
            dueDay: (int) $input['due_day'],
            durationMonths: $durationMonths,
            adjustmentIndex: (string) $input['adjustment_index'],
            lateFeePercent: (int) $input['late_fee_percent'],
            terminationNoticeDays: (int) $input['termination_notice_days'],
            services: array_map(
                static fn (string $service): string => AccountingService::from($service)->label(),
                array_values($input['services']),
            ),
            includesLgpd: (bool) ($input['includes_lgpd'] ?? false),
            includesConfidentiality: (bool) ($input['includes_confidentiality'] ?? false),
            additionalTerms: $this->nullableString($input['additional_terms'] ?? null),
            issuedAt: new DateTimeImmutable('today'),
            startsAt: $startsAt,
            endsAt: $startsAt->modify(sprintf('+%d months -1 day', $durationMonths)),
        );
    }

    private function nullableString(mixed $value): ?string
    {
        $normalized = trim((string) $value);

        return $normalized === '' ? null : $normalized;
    }
}
