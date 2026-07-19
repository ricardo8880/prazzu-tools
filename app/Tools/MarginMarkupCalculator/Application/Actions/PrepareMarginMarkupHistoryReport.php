<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Application\Actions;

use Symfony\Component\HttpKernel\Exception\HttpException;

final readonly class PrepareMarginMarkupHistoryReport
{
    public function __construct(private RequireOwnedMarginMarkupRun $ownedRun) {}

    /** @return array{result:array<string,mixed>,input:array<string,mixed>,generatedAt:string} */
    public function execute(string $runId, int $userId): array
    {
        $run = $this->ownedRun->execute($runId, $userId);
        $result = $run->result;
        if (($result['calculation_type'] ?? 'single') !== 'single') {
            throw new HttpException(422, 'O PDF está disponível para cálculos individuais.');
        }

        return ['result' => $result, 'input' => $run->input, 'generatedAt' => $run->finishedAt->format('d/m/Y H:i')];
    }
}
