<?php

declare(strict_types=1);

namespace App\Core\Export\Services;

use App\Core\Export\Data\PdfDocument;
use App\Core\Export\Data\SpreadsheetDocument;
use App\Core\Export\Data\SpreadsheetSheet;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;

final class ToolResultExportFactory
{
    public function __construct(private readonly HumanReadableExportPresenter $presenter) {}

    /** @param array<string, mixed> $input */
    public function pdf(string $title, string $filename, ToolCalculationResult $result, array $input = []): PdfDocument
    {
        $payload = $result->toArray();

        return new PdfDocument(
            filename: $filename,
            view: 'exports.tool-result',
            data: [
                'title' => $title,
                'summary' => $payload['summary'] ?? [],
                'inputRows' => $this->presenter->rows($input),
                'detailRows' => $this->presenter->rows(
                    is_array($payload['details'] ?? null) ? $payload['details'] : [],
                    skipDuplicatedInput: true,
                ),
                'memoryRows' => $this->presenter->memoryRows(
                    is_array($payload['calculation_memory'] ?? null) ? $payload['calculation_memory'] : [],
                ),
                'warnings' => $payload['warnings'] ?? [],
            ],
        );
    }

    /** @param array<string, mixed> $input */
    public function spreadsheet(string $filename, ToolCalculationResult $result, array $input = []): SpreadsheetDocument
    {
        $payload = $result->toArray();
        $sheets = [new SpreadsheetSheet('Resumo', $this->summaryRows($payload))];

        if ($input !== []) {
            $sheets[] = new SpreadsheetSheet('Dados informados', $this->readableRows($this->presenter->rows($input)));
        }

        $details = is_array($payload['details'] ?? null) ? $payload['details'] : [];
        if ($details !== []) {
            $sheets[] = new SpreadsheetSheet('Detalhamento', $this->readableRows($this->presenter->rows($details, true)));
        }

        $memory = is_array($payload['calculation_memory'] ?? null) ? $payload['calculation_memory'] : [];
        if ($memory !== []) {
            $rows = [['Etapa', 'Fórmula', 'Resultado']];
            foreach ($this->presenter->memoryRows($memory) as $step) {
                $rows[] = [$step['label'], $step['formula'], $step['result']];
            }
            $sheets[] = new SpreadsheetSheet('Memória de cálculo', $rows);
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
                $this->presenter->formatValue((string) ($item['key'] ?? ''), $item['value'] ?? null),
                (string) ($item['description'] ?? ''),
            ];
        }

        return $rows;
    }

    /** @param list<array{label:string,value:string,level:int}> $items @return list<list<string|int|float|bool|null>> */
    private function readableRows(array $items): array
    {
        $rows = [['Campo', 'Valor']];
        foreach ($items as $item) {
            $rows[] = [str_repeat('  ', $item['level']).$item['label'], $item['value']];
        }

        return $rows;
    }
}
