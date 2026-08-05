<?php

declare(strict_types=1);

namespace App\Core\Export\Services;

use App\Core\Export\Data\PdfDocument;
use App\Core\Export\Data\SpreadsheetDocument;
use App\Core\Export\Data\SpreadsheetSheet;

final class StructuredResultExportFactory
{
    public function __construct(private readonly HumanReadableExportPresenter $presenter) {}

    /** @param array<string,mixed> $result @param array<string,mixed> $input */
    public function pdf(string $title, string $filename, array $result, array $input = []): PdfDocument
    {
        return new PdfDocument($filename, 'exports.structured-result', [
            'title' => $title,
            'inputRows' => $this->presenter->rows($input),
            'resultRows' => $this->presenter->rows($result, true),
        ]);
    }

    /** @param array<string,mixed> $result @param array<string,mixed> $input */
    public function spreadsheet(string $filename, array $result, array $input = []): SpreadsheetDocument
    {
        $sheets = [new SpreadsheetSheet('Resultado', $this->rows($result, true))];
        if ($input !== []) {
            $sheets[] = new SpreadsheetSheet('Dados informados', $this->rows($input));
        }

        return new SpreadsheetDocument($filename, $sheets);
    }

    /** @param array<string,mixed> $data @return list<list<string|int|float|bool|null>> */
    private function rows(array $data, bool $skipDuplicatedInput = false): array
    {
        $rows = [['Campo', 'Valor']];
        foreach ($this->presenter->rows($data, $skipDuplicatedInput) as $item) {
            $rows[] = [str_repeat('  ', $item['level']).$item['label'], $item['value']];
        }

        return $rows;
    }
}
