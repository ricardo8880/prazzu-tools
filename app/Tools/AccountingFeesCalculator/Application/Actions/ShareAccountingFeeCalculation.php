<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Application\Actions;

use App\Tools\AccountingFeesCalculator\Application\Access\AccountingFeesRecordAccess;
use App\Tools\AccountingFeesCalculator\Application\Data\AccountingFeesOwner;
use App\Tools\AccountingFeesCalculator\Infrastructure\Models\AccountingFeeCalculation;
use App\Tools\AccountingFeesCalculator\Infrastructure\Repositories\AccountingFeeCalculationRepository;
use Illuminate\Support\Str;

final readonly class ShareAccountingFeeCalculation
{
    public function __construct(
        private AccountingFeesRecordAccess $access,
        private AccountingFeeCalculationRepository $calculations,
    ) {}

    public function execute(AccountingFeeCalculation $calculation, AccountingFeesOwner $owner): string
    {
        $this->access->ensureOwnedBy($owner, $calculation->user_id, $calculation->session_key);

        if ($calculation->share_token !== null) {
            return (string) $calculation->share_token;
        }

        return $this->calculations->setShareToken($calculation, (string) Str::uuid());
    }
}
