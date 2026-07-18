<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Application\Actions;

use App\Tools\AccountingFeesCalculator\Infrastructure\Models\AccountingFeeCalculation;
use App\Tools\AccountingFeesCalculator\Infrastructure\Repositories\AccountingFeeCalculationRepository;

final readonly class GetSharedAccountingFeeCalculation
{
    public function __construct(private AccountingFeeCalculationRepository $calculations) {}

    public function execute(string $token): AccountingFeeCalculation
    {
        return $this->calculations->findShared($token);
    }
}
