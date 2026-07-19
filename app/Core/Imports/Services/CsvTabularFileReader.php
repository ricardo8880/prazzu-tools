<?php

declare(strict_types=1);

namespace App\Core\Imports\Services;

use App\Core\Imports\Contracts\TabularFileReader;
use App\Core\Imports\Data\TabularDataset;
use App\Core\Imports\Exceptions\InvalidImportFile;
use Illuminate\Http\UploadedFile;
use SplFileObject;

final class CsvTabularFileReader implements TabularFileReader
{
    public function supports(string $extension): bool
    {
        return in_array(strtolower($extension), ['csv', 'txt'], true);
    }

    public function read(UploadedFile $file, int $maximumRows): TabularDataset
    {
        $handle = new SplFileObject($file->getRealPath(), 'r');
        $handle->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);
        $handle->setCsvControl($this->detectDelimiter($file->getRealPath()));

        $headers = [];
        $rows = [];

        foreach ($handle as $index => $values) {
            if (! is_array($values) || $values === [null]) {
                continue;
            }

            $values = array_map(fn ($value): ?string => $this->normalizeCell($value), $values);

            if ($headers === []) {
                $headers = $this->normalizeHeaders($values);

                continue;
            }

            if (count($rows) >= $maximumRows) {
                throw new InvalidImportFile("O arquivo ultrapassa o limite de {$maximumRows} linhas.");
            }

            $rows[] = $this->combine($headers, $values);
        }

        if ($headers === []) {
            throw new InvalidImportFile('O arquivo não possui cabeçalho legível.');
        }

        return new TabularDataset($headers, $rows, $file->getClientOriginalName(), 'csv');
    }

    private function detectDelimiter(string $path): string
    {
        $sample = (string) file_get_contents($path, false, null, 0, 4096);
        $scores = [',' => substr_count($sample, ','), ';' => substr_count($sample, ';'), "\t" => substr_count($sample, "\t")];
        arsort($scores);

        return (string) array_key_first($scores);
    }

    /** @param list<?string> $headers */
    private function normalizeHeaders(array $headers): array
    {
        $normalized = [];
        foreach ($headers as $index => $header) {
            $base = trim((string) $header) !== '' ? trim((string) $header) : 'Coluna '.($index + 1);
            $candidate = $base;
            $suffix = 2;
            while (in_array($candidate, $normalized, true)) {
                $candidate = $base.' '.$suffix++;
            }
            $normalized[] = $candidate;
        }

        return $normalized;
    }

    /** @param list<string> $headers @param list<?string> $values */
    private function combine(array $headers, array $values): array
    {
        $row = [];
        foreach ($headers as $index => $header) {
            $row[$header] = $values[$index] ?? null;
        }

        return $row;
    }

    private function normalizeCell(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $text = trim((string) $value);
        $text = preg_replace('/^\xEF\xBB\xBF/', '', $text) ?? $text;

        return $text === '' ? null : $text;
    }
}
