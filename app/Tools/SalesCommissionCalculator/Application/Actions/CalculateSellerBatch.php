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
                throw ValidationException::withMessages(['seller_batch' => 'Linha '.($lineNumber + 1).': use Nome;Vendas;Estornos.']);
            }
            [$name, $sales] = $columns;
            $reversals = $columns[2] ?? '0';
            $results[] = [
                'name' => $name !== '' ? $name : 'Vendedor '.($lineNumber + 1),
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
            throw ValidationException::withMessages(['seller_batch' => 'Informe de 2 a 50 vendedores para o processamento em lote.']);
        }

        return $results;
    }
}
