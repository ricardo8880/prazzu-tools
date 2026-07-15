<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Application\Actions;

use App\Core\Money\Money;
use App\Tools\AccountingFeesCalculator\Domain\Data\CommercialProposal;
use App\Tools\AccountingFeesCalculator\Domain\Enums\AccountingService;
use DateTimeImmutable;

final class BuildCommercialProposal
{
    /** @param array<string, mixed> $input */
    public function execute(array $input): CommercialProposal
    {
        $issuedAt = new DateTimeImmutable('today');
        $validityDays = (int) $input['validity_days'];

        return new CommercialProposal(
            clientCompany: trim((string) $input['client_company']),
            clientDocument: $this->nullableString($input['client_document'] ?? null),
            contactName: trim((string) $input['contact_name']),
            accountingFirm: trim((string) $input['accounting_firm']),
            monthlyFee: Money::fromDecimal((string) $input['monthly_fee']),
            setupFee: Money::fromDecimal($this->decimalOrZero($input['setup_fee'] ?? null)),
            dueDay: (int) $input['due_day'],
            validityDays: $validityDays,
            services: array_map(
                static fn (string $service): string => AccountingService::from($service)->label(),
                array_values($input['services']),
            ),
            notes: $this->nullableString($input['notes'] ?? null),
            issuedAt: $issuedAt,
            validUntil: $issuedAt->modify(sprintf('+%d days', $validityDays)),
        );
    }

    private function decimalOrZero(mixed $value): string
    {
        $normalized = trim((string) $value);

        return $normalized === '' ? '0' : $normalized;
    }

    private function nullableString(mixed $value): ?string
    {
        $normalized = trim((string) $value);

        return $normalized === '' ? null : $normalized;
    }
}
