<?php

namespace App\Core\Tools\Infrastructure\Services;

use App\Core\Tools\Contracts\ToolModule;
use App\Core\Tools\Infrastructure\Contracts\ToolResultExporter;
use InvalidArgumentException;

final class ManifestToolResultExporter implements ToolResultExporter
{
    public function export(ToolModule $module, array $result, string $format): string
    {
        $policy = $module->manifest()->export;

        if ($policy === null || ! $policy->enabled || ! in_array($format, $policy->formats, true)) {
            throw new InvalidArgumentException('Formato não autorizado pelo manifesto da ferramenta.');
        }

        return match ($format) {
            'json' => json_encode($result, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            'csv' => $this->toCsv($result),
            default => throw new InvalidArgumentException('O formato exige um adaptador central binário ou de impressão.'),
        };
    }

    /** @param array<string, mixed> $result */
    private function toCsv(array $result): string
    {
        $stream = fopen('php://temp', 'w+b');

        if ($stream === false) {
            throw new InvalidArgumentException('Não foi possível criar o arquivo temporário de exportação.');
        }

        fputcsv($stream, array_keys($result), ';');
        fputcsv($stream, array_map(
            static fn (mixed $value): string => is_scalar($value) || $value === null
                ? (string) $value
                : json_encode($value, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
            array_values($result),
        ), ';');

        rewind($stream);
        $contents = stream_get_contents($stream);
        fclose($stream);

        return $contents === false ? '' : $contents;
    }
}
