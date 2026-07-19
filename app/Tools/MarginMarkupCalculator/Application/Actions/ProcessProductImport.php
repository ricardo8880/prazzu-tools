<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Application\Actions;

use App\Core\Imports\Contracts\ImportDatasetStore;
use RuntimeException;

final readonly class ProcessProductImport
{
    public function __construct(private ImportDatasetStore $store) {}

    public function execute(array $data, string $ownerKey): array
    {
        $token = (string) ($data['import_token'] ?? '');
        $dataset = $this->store->get($token, $ownerKey);
        if ($dataset === null) {
            throw new RuntimeException('A pré-visualização expirou. Importe o arquivo novamente.');
        }

        $products = [];
        $rejected = [];
        foreach ($dataset->rows as $index => $row) {
            $product = [
                'name' => $this->cell($row, $data['name_column'] ?? null),
                'code' => $this->cell($row, $data['code_column'] ?? null),
                'category' => $this->cell($row, $data['category_column'] ?? null),
                'base_cost' => $this->cell($row, $data['base_cost_column'] ?? null),
                'additional_costs' => $this->cell($row, $data['additional_costs_column'] ?? null, '0,00'),
                'freight_cost' => $this->cell($row, $data['freight_cost_column'] ?? null, '0,00'),
                'packaging_cost' => $this->cell($row, $data['packaging_cost_column'] ?? null, '0,00'),
                'fixed_expenses' => $this->cell($row, $data['fixed_expenses_column'] ?? null, '0,00'),
                'desired_margin' => $this->cell($row, $data['desired_margin_column'] ?? null, '30'),
                'taxes_percentage' => $this->cell($row, $data['taxes_percentage_column'] ?? null, '0'),
                'commission_percentage' => $this->cell($row, $data['commission_percentage_column'] ?? null, '0'),
                'card_fees_percentage' => $this->cell($row, $data['card_fees_percentage_column'] ?? null, '0'),
                'marketplace_fees_percentage' => $this->cell($row, $data['marketplace_fees_percentage_column'] ?? null, '0'),
            ];

            $errors = [];
            if ($product['name'] === '') {
                $errors[] = 'nome do produto vazio';
            }
            if ($product['base_cost'] === '') {
                $errors[] = 'custo base vazio';
            }

            if ($errors !== []) {
                $rejected[] = ['line' => $index + 2, 'reason' => implode('; ', $errors)];

                continue;
            }

            $products[] = $product;
        }

        if ($products === []) {
            throw new RuntimeException('Nenhuma linha válida foi encontrada para importação.');
        }

        $this->store->forget($token, $ownerKey);

        return ['products' => $products, 'rejected' => $rejected, 'imported' => count($products)];
    }

    private function cell(array $row, mixed $column, string $default = ''): string
    {
        if (! is_string($column) || $column === '') {
            return $default;
        }

        $value = trim((string) ($row[$column] ?? ''));

        return $value === '' ? $default : $value;
    }
}
