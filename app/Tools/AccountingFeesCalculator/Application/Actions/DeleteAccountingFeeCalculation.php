<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Application\Actions;

use App\Tools\AccountingFeesCalculator\Application\Access\AccountingFeesRecordAccess;
use App\Tools\AccountingFeesCalculator\Application\Data\AccountingFeesOwner;
use App\Tools\AccountingFeesCalculator\Infrastructure\Models\AccountingFeeCalculation;
use App\Tools\AccountingFeesCalculator\Infrastructure\Repositories\AccountingFeeCalculationRepository;

final readonly class DeleteAccountingFeeCalculation
{
    public function __construct(
        private AccountingFeesRecordAccess $access,
        private AccountingFeeCalculationRepository $calculations,
    ) {}

    public function execute(AccountingFeeCalculation $calculation, AccountingFeesOwner $owner): void
    {
        $this->access->ensureOwnedBy($owner, $calculation->user_id, $calculation->session_key);
        $this->calculations->delete($calculation);
    }
}
