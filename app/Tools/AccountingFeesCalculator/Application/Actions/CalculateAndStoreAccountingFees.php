<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Application\Actions;

use App\Core\Dates\ReferenceDate;
use App\Core\Tools\History\Contracts\ToolRunRecorder;
use App\Core\Tools\History\Data\RuleVersion;
use App\Core\Tools\History\Data\ToolRunHandle;
use App\Tools\AccountingFeesCalculator\Domain\Calculators\AccountingFeesCalculator;
use App\Tools\AccountingFeesCalculator\Tool;
use Throwable;

final readonly class CalculateAndStoreAccountingFees
{
    public function __construct(
        private CalculateAccountingFees $calculate,
        private ToolRunRecorder $recorder,
        private Tool $module,
    ) {}

    /** @param array<string,mixed> $input @return array{result:array<string,mixed>,saved:bool} */
    public function execute(array $input, ?int $userId, bool $persist): array
    {
        $run = null;
        $historyInput = ['run_type' => ManageAccountingFeesHistory::TYPE_CALCULATION, ...$input];

        try {
            if ($persist && $userId !== null) {
                $run = $this->recorder->start(
                    $this->module,
                    new RuleVersion(AccountingFeesCalculator::RULE_VERSION),
                    ReferenceDate::fromDateTime(now()),
                    $historyInput,
                    $userId,
                );
            }

            $result = $this->calculate->execute($input)->toArray();
            if ($run !== null) {
                $this->recorder->succeed($run, $result);
            }

            return ['result' => $result, 'saved' => $run !== null];
        } catch (Throwable $exception) {
            $this->fail($run);
            throw $exception;
        }
    }

    private function fail(?ToolRunHandle $run): void
    {
        if ($run !== null) {
            $this->recorder->fail($run, 'accounting_fee.calculation_failed');
        }
    }
}
