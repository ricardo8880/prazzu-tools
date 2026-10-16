<?php

declare(strict_types=1);

namespace App\Tools\SalesCommissionCalculator\Application\Actions;

use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use Illuminate\Validation\ValidationException;

final readonly class CalculateSellerBatch
{
    public const FEATURE = 'batch_sellers';

    public function __construct(private CalculateTool $calculate) {}

    /** @param array<string,mixed> $data
     *  @return array<int,array{name:string,result:ToolCalculationResult}>
     */
    public function execute(array $data): array
    {
        $lines = preg_split('/\R/u', trim((string) ($data['seller_batch'] ?? ''))) ?: [];
        $results = [];
        foreach ($lines as $lineNumber => $line) {
            if (trim($line) === '') {
                continue;
            }
            $columns = array_map('trim', str_getcsv($line, ';'));
            if (count($columns) < 2) {
                throw ValidationException::withMessages(['seller_batch' => 'Line '.($lineNumber + 1).': use Name;Sales;Refunds.']);
            }
            [$name, $sales] = $columns;
            $reversals = $columns[2] ?? '0';
            $results[] = [
                'name' => $name !== '' ? $name : 'Seller '.($lineNumber + 1),
                'result' => $this->calculate->execute([
                    'sales' => $sales,
                    'reversals' => $reversals,
                    'rate' => (string) $data['rate'],
                    'goal' => (string) $data['goal'],
                    'goal_bonus_rate' => (string) $data['goal_bonus_rate'],
                ]),
            ];
        }
        if (count($results) < 2 || count($results) > 50) {
            throw ValidationException::withMessages(['seller_batch' => 'Enter 2 to 50 sellers for batch processing.']);
        }

        return $results;
    }
}
