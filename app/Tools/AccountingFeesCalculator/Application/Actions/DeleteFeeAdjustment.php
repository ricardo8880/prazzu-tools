<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Application\Actions;

use App\Tools\AccountingFeesCalculator\Application\Access\AccountingFeesRecordAccess;
use App\Tools\AccountingFeesCalculator\Application\Data\AccountingFeesOwner;
use App\Tools\AccountingFeesCalculator\Infrastructure\Models\FeeAdjustment;
use App\Tools\AccountingFeesCalculator\Infrastructure\Repositories\FeeAdjustmentRepository;

final readonly class DeleteFeeAdjustment
{
    public function __construct(
        private AccountingFeesRecordAccess $access,
        private FeeAdjustmentRepository $adjustments,
    ) {}

    public function execute(FeeAdjustment $adjustment, AccountingFeesOwner $owner): void
    {
        $this->access->ensureOwnedBy($owner, $adjustment->user_id, $adjustment->session_key);
        $this->adjustments->delete($adjustment);
    }
}
