<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Application\Actions;

final readonly class CalculateMarginMarkupBatch
{
    public function __construct(private CalculateMarginMarkup $calculator) {}

    /**
     * @param array<int, array<string, string|null>> $products
     * @return array<int, array<string, string>>
     */
    public function execute(array $products): array
    {
        $results = [];

        foreach ($products as $index => $product) {
            $result = $this->calculator->execute($product)->toArray();
            $results[] = array_merge([
                'row' => (string) ($index + 1),
                'name' => trim((string) $product['name']),
                'code' => trim((string) ($product['code'] ?? '')),
                'category' => trim((string) ($product['category'] ?? '')),
            ], $result);
        }

        return $results;
    }
}
