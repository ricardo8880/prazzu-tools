<?php

declare(strict_types=1);

namespace App\Core\Export\Services;

use App\Core\Export\Data\PdfDocument;
use App\Core\Export\Data\SpreadsheetDocument;
use App\Core\Export\Data\SpreadsheetSheet;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;

final class ToolResultExportFactory
{
    /** @param array<string, mixed> $input */
    public function pdf(string $title, string $filename, ToolCalculationResult $result, array $input = []): PdfDocument
    {
        return new PdfDocument(
            filename: $filename,
            view: 'exports.tool-result',
            data: [
                'title' => $title,
                'input' => $input,
                'result' => $result->toArray(),
            ],
        );
    }

    /** @param array<string, mixed> $input */
    public function spreadsheet(string $filename, ToolCalculationResult $result, array $input = []): SpreadsheetDocument
    {
        $payload = $result->toArray();
        $sheets = [
            new SpreadsheetSheet('Resumo', $this->summaryRows($payload)),
        ];

        if ($input !== []) {
            $sheets[] = new SpreadsheetSheet('Dados informados', $this->keyValueRows($input));
        }

        $details = $payload['details'] ?? [];
        if (is_array($details) && $details !== []) {
            $sheets[] = new SpreadsheetSheet('Detalhamento', $this->keyValueRows($details));
        }

        $memory = $payload['calculation_memory'] ?? null;
        if (is_array($memory) && $memory !== []) {
            $sheets[] = new SpreadsheetSheet('Memória de cálculo', $this->memoryRows($memory));
        }

        return new SpreadsheetDocument(filename: $filename, sheets: $sheets);
    }

    /** @param array<string, mixed> $payload @return list<list<string|int|float|bool|null>> */
    private function summaryRows(array $payload): array
    {
        $rows = [['Indicador', 'Valor', 'Descrição']];
        foreach (($payload['summary'] ?? []) as $item) {
            if (! is_array($item)) {
                continue;
            }
            $rows[] = [
                (string) ($item['label'] ?? $item['key'] ?? ''),
                $this->scalar($item['value'] ?? null),
                (string) ($item['description'] ?? ''),
            ];
        }
        return $rows;
    }

    /** @param array<string, mixed> $data @return list<list<string|int|float|bool|null>> */
    private function keyValueRows(array $data): array
    {
        $rows = [['Campo', 'Valor']];
        $this->flatten($data, '', $rows);
        return $rows;
    }

    /** @param array<string, mixed> $data @param list<list<string|int|float|bool|null>> $rows */
    private function flatten(array $data, string $prefix, array &$rows): void
    {
        foreach ($data as $key => $value) {
            $path = $prefix === '' ? (string) $key : $prefix.'.'.$key;
            if (is_array($value)) {
                $this->flatten($value, $path, $rows);
                continue;
            }
            $rows[] = [$path, $this->scalar($value)];
        }
    }

    /** @param array<string, mixed> $memory @return list<list<string|int|float|bool|null>> */
    private function memoryRows(array $memory): array
    {
        $rows = [['Etapa', 'Fórmula', 'Resultado']];
        foreach (($memory['steps'] ?? []) as $step) {
            if (! is_array($step)) {
                continue;
            }
            $rows[] = [
                (string) ($step['label'] ?? ''),
                (string) ($step['formula'] ?? ''),
                $this->scalar($step['result'] ?? null),
            ];
        }
        return $rows;
    }

    private function scalar(mixed $value): string|int|float|bool|null
    {
        if ($value === null || is_string($value) || is_int($value) || is_float($value) || is_bool($value)) {
            return $value;
        }
        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '';
    }
}
