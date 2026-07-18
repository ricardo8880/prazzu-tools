<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Application\Actions;

use App\Tools\AccountingFeesCalculator\Application\Data\AccountingFeesOwner;
use App\Tools\AccountingFeesCalculator\Infrastructure\Repositories\AccountingFeeCalculationRepository;

final readonly class CalculateAndStoreAccountingFees
{
    public function __construct(
        private CalculateAccountingFees $calculate,
        private AccountingFeeCalculationRepository $calculations,
    ) {}

    /**
     * @param  array<string, mixed>  $input
     * @return array{result: array<string, mixed>, saved: bool}
     */
    public function execute(array $input, AccountingFeesOwner $owner): array
    {
        $result = $this->calculate->execute($input)->toArray();
        $saved = $owner->userId !== null;

        if ($saved) {
            $this->calculations->store($owner, $input, $result);
        }

        return ['result' => $result, 'saved' => $saved];
    }
}
