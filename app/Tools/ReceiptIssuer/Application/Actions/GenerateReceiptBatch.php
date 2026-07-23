<?php

declare(strict_types=1);

namespace App\Tools\ReceiptIssuer\Application\Actions;

use App\Core\Imports\Services\CsvTabularFileReader;
use App\Tools\ReceiptIssuer\Presentation\Requests\ExecuteToolRequest;
use Illuminate\Support\Facades\Validator;
use Throwable;

final class GenerateReceiptBatch
{
    public const MAXIMUM_ROWS = 100;

    public function __construct(
        private readonly CsvTabularFileReader $reader,
        private readonly BuildCalculationInput $build,
        private readonly CalculateTool $calculate,
    ) {}

    /** @return array{receipts:list<array<string,mixed>>,errors:list<array{line:int,message:string}>,total:int} */
    public function execute(\Illuminate\Http\UploadedFile $file): array
    {
        $dataset = $this->reader->read($file, self::MAXIMUM_ROWS);
        $receipts = [];
        $errors = [];
        $rules = (new ExecuteToolRequest)->rules();

        foreach ($dataset->rows as $index => $row) {
            $data = $this->normalize($row);
            $validator = Validator::make($data, $rules, (new ExecuteToolRequest)->messages());

            if ($validator->fails()) {
                $errors[] = ['line' => $index + 2, 'message' => $validator->errors()->first()];
                continue;
            }

            try {
                $result = $this->calculate->execute($this->build->execute($validator->validated()))->toArray();
                $receipt = $result['details']['receipt'] ?? null;
                if (! is_array($receipt)) {
                    throw new \RuntimeException('O recibo não pôde ser montado.');
                }
                $receipts[] = $receipt;
            } catch (Throwable $exception) {
                $errors[] = ['line' => $index + 2, 'message' => $exception->getMessage()];
            }
        }

        return ['receipts' => $receipts, 'errors' => $errors, 'total' => count($dataset->rows)];
    }

    /** @param array<string, mixed> $row @return array<string, mixed> */
    private function normalize(array $row): array
    {
        $normalized = [];
        foreach ($row as $header => $value) {
            $key = strtolower(trim((string) $header));
            $key = str_replace([' ', '-'], '_', $key);
            $normalized[$key] = is_string($value) ? trim($value) : $value;
        }

        return $normalized;
    }
}
