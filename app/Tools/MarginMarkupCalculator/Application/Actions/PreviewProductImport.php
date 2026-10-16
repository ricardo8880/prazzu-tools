<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Application\Actions;

use App\Core\Imports\Contracts\ImportDatasetStore;
use App\Core\Imports\Services\CompositeTabularFileReader;
use Illuminate\Http\UploadedFile;

final readonly class PreviewProductImport
{
    private const MAXIMUM_ROWS = 100;

    public function __construct(
        private CompositeTabularFileReader $reader,
        private ImportDatasetStore $store,
    ) {}

    public function execute(UploadedFile $file, string $ownerKey): array
    {
        $dataset = $this->reader->read($file, self::MAXIMUM_ROWS);
        $token = $this->store->put($dataset, $ownerKey);

        return [
            'token' => $token,
            'file_name' => $dataset->originalName,
            'format' => strtoupper($dataset->format),
            'headers' => $dataset->headers,
            'preview_rows' => array_slice($dataset->rows, 0, 8),
            'total_rows' => count($dataset->rows),
            'maximum_rows' => self::MAXIMUM_ROWS,
            'suggested_mapping' => $this->suggestMapping($dataset->headers),
        ];
    }

    private function suggestMapping(array $headers): array
    {
        $patterns = [
            'name_column' => ['product', 'name', 'product name', 'description'],
            'code_column' => ['code', 'sku', 'reference'],
            'category_column' => ['category', 'group'],
            'base_cost_column' => ['base cost', 'cost', 'unit cost', 'cost price'],
            'additional_costs_column' => ['additional costs', 'other costs'],
            'freight_cost_column' => ['shipping', 'freight'],
            'packaging_cost_column' => ['packaging'],
            'fixed_expenses_column' => ['expenses', 'allocated expenses', 'fixed costs'],
            'desired_margin_column' => ['margin', 'desired margin', 'margin %'],
            'taxes_percentage_column' => ['taxes', 'tax', 'taxes %'],
            'commission_percentage_column' => ['commission', 'commission %'],
            'card_fees_percentage_column' => ['card fees', 'card fee', 'card fees %'],
            'marketplace_fees_percentage_column' => ['marketplace', 'marketplace fee', 'marketplace fees %'],
        ];

        $suggestions = [];
        foreach ($patterns as $field => $aliases) {
            $normalizedAliases = array_map($this->normalize(...), $aliases);
            foreach ($headers as $header) {
                if (in_array($this->normalize($header), $normalizedAliases, true)) {
                    $suggestions[$field] = $header;
                    break;
                }
            }
        }

        return $suggestions;
    }

    private function normalize(string $value): string
    {
        $value = mb_strtolower(trim($value));
        $transliterated = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);

        return preg_replace('/[^a-z0-9]+/', ' ', $transliterated ?: $value) ?: $value;
    }
}
