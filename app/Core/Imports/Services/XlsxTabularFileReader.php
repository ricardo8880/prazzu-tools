<?php

declare(strict_types=1);

namespace App\Core\Imports\Services;

use App\Core\Imports\Contracts\TabularFileReader;
use App\Core\Imports\Data\TabularDataset;
use App\Core\Imports\Exceptions\InvalidImportFile;
use Illuminate\Http\UploadedFile;
use ZipArchive;

final class XlsxTabularFileReader implements TabularFileReader
{
    public function supports(string $extension): bool
    {
        return strtolower($extension) === 'xlsx';
    }

    public function read(UploadedFile $file, int $maximumRows): TabularDataset
    {
        if (! class_exists(ZipArchive::class) || ! function_exists('simplexml_load_string')) {
            throw new InvalidImportFile('A leitura de Excel exige as extensões ZIP e SimpleXML no servidor.');
        }

        $zip = new ZipArchive;
        if ($zip->open($file->getRealPath()) !== true) {
            throw new InvalidImportFile('Não foi possível abrir a planilha Excel.');
        }

        try {
            $sharedStrings = $this->sharedStrings($zip);
            $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
            if (! is_string($sheetXml)) {
                throw new InvalidImportFile('A primeira aba da planilha não pôde ser lida.');
            }

            $sheet = simplexml_load_string($sheetXml);
            if ($sheet === false) {
                throw new InvalidImportFile('A estrutura da planilha é inválida.');
            }

            $rawRows = [];
            foreach ($sheet->sheetData->row as $row) {
                $values = [];
                foreach ($row->c as $cell) {
                    $attributes = $cell->attributes();
                    $reference = (string) ($attributes['r'] ?? 'A1');
                    $column = $this->columnIndex($reference);
                    $type = (string) ($attributes['t'] ?? '');
                    $value = (string) ($cell->v ?? '');
                    $values[$column] = $type === 's' ? ($sharedStrings[(int) $value] ?? null) : ($value === '' ? null : $value);
                }
                if ($values !== []) {
                    ksort($values);
                    $rawRows[] = $values;
                }
            }
        } finally {
            $zip->close();
        }

        if ($rawRows === []) {
            throw new InvalidImportFile('A planilha está vazia.');
        }

        $headerValues = array_shift($rawRows);
        $lastColumn = max(array_keys($headerValues));
        $headers = [];
        for ($index = 0; $index <= $lastColumn; $index++) {
            $headers[] = trim((string) ($headerValues[$index] ?? '')) ?: 'Coluna '.($index + 1);
        }
        $headers = $this->deduplicateHeaders($headers);

        if (count($rawRows) > $maximumRows) {
            throw new InvalidImportFile("O arquivo ultrapassa o limite de {$maximumRows} linhas.");
        }

        $rows = [];
        foreach ($rawRows as $rawRow) {
            $row = [];
            foreach ($headers as $index => $header) {
                $value = isset($rawRow[$index]) ? trim((string) $rawRow[$index]) : null;
                $row[$header] = $value === '' ? null : $value;
            }
            $rows[] = $row;
        }

        return new TabularDataset($headers, $rows, $file->getClientOriginalName(), 'xlsx');
    }

    private function sharedStrings(ZipArchive $zip): array
    {
        $xml = $zip->getFromName('xl/sharedStrings.xml');
        if (! is_string($xml)) {
            return [];
        }

        $document = simplexml_load_string($xml);
        if ($document === false) {
            return [];
        }

        $strings = [];
        foreach ($document->si as $item) {
            if (isset($item->t)) {
                $strings[] = (string) $item->t;

                continue;
            }

            $parts = [];
            foreach ($item->r as $run) {
                $parts[] = (string) $run->t;
            }
            $strings[] = implode('', $parts);
        }

        return $strings;
    }

    private function columnIndex(string $reference): int
    {
        preg_match('/^([A-Z]+)/i', $reference, $matches);
        $letters = strtoupper($matches[1] ?? 'A');
        $index = 0;
        foreach (str_split($letters) as $letter) {
            $index = ($index * 26) + (ord($letter) - 64);
        }

        return $index - 1;
    }

    private function deduplicateHeaders(array $headers): array
    {
        $result = [];
        foreach ($headers as $header) {
            $candidate = $header;
            $suffix = 2;
            while (in_array($candidate, $result, true)) {
                $candidate = $header.' '.$suffix++;
            }
            $result[] = $candidate;
        }

        return $result;
    }
}
