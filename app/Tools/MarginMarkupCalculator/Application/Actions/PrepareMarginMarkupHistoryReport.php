<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Application\Actions;

use App\Core\Tools\History\Models\ToolRun;
use Symfony\Component\HttpKernel\Exception\HttpException;

final readonly class PrepareMarginMarkupHistoryReport
{
    public function __construct(private RequireOwnedMarginMarkupRun $ownedRun) {}

    /** @return array{result: array<string, mixed>, input: array<string, mixed>, generatedAt: string} */
    public function execute(ToolRun $run, int $userId): array
    {
        $run = $this->ownedRun->execute($run, $userId);
        $result = $run->result_payload ?? [];

        if (($result['calculation_type'] ?? 'single') !== 'single') {
            throw new HttpException(422, 'O PDF está disponível para cálculos individuais.');
        }

        return [
            'result' => $result,
            'input' => $run->input_payload ?? [],
            'generatedAt' => $run->finished_at?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i'),
        ];
    }
}
