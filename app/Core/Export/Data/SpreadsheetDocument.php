<?php

declare(strict_types=1);

namespace App\Core\Export\Data;

use InvalidArgumentException;

final readonly class SpreadsheetDocument
{
    /**
     * @param non-empty-list<SpreadsheetSheet> $sheets
     */
    public function __construct(
        public string $filename,
        public array $sheets,
    ) {
        if (trim($this->filename) === '') {
            throw new InvalidArgumentException('O nome do arquivo Excel não pode ser vazio.');
        }

        if ($this->sheets === []) {
            throw new InvalidArgumentException('O arquivo Excel deve possuir ao menos uma planilha.');
        }

        foreach ($this->sheets as $sheet) {
            if (! $sheet instanceof SpreadsheetSheet) {
                throw new InvalidArgumentException('Todas as entradas devem ser instâncias de SpreadsheetSheet.');
            }
        }

        $names = array_map(static fn (SpreadsheetSheet $sheet): string => strtolower($sheet->name), $this->sheets);
        if (count($names) !== count(array_unique($names))) {
            throw new InvalidArgumentException('Os nomes das planilhas devem ser únicos.');
        }
    }

    public function downloadFilename(): string
    {
        $filename = trim($this->filename);

        return str_ends_with(strtolower($filename), '.xlsx') ? $filename : $filename.'.xlsx';
    }
}
