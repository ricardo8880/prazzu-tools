<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Application\Actions;

use App\Tools\AccountingFeesCalculator\Application\Access\AccountingFeesRecordAccess;
use App\Tools\AccountingFeesCalculator\Application\Data\AccountingFeesOwner;
use App\Tools\AccountingFeesCalculator\Infrastructure\Models\AccountingFeeCalculation;

final readonly class DuplicateAccountingFeeCalculation
{
    public function __construct(private AccountingFeesRecordAccess $access) {}

    /** @return array{input: array<string, mixed>, result: array<string, mixed>} */
    public function execute(AccountingFeeCalculation $calculation, AccountingFeesOwner $owner): array
    {
        $this->access->ensureOwnedBy($owner, $calculation->user_id, $calculation->session_key);

        return [
            'input' => $calculation->input,
            'result' => $calculation->result,
        ];
    }
}
