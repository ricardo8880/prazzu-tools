<?php

declare(strict_types=1);

namespace App\Tools\EmployeeCostCalculator\Application\Actions;

use App\Core\Imports\Contracts\ImportDatasetStore;
use App\Core\Imports\Services\CompositeTabularFileReader;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

final readonly class PreviewEmployeeImport
{
    public function __construct(
        private CompositeTabularFileReader $reader,
        private ImportDatasetStore $store,
    ) {}

    public function execute(UploadedFile $file, string $ownerKey): array
    {
        $dataset = $this->reader->read($file, 500);

        return [
            'token' => $this->store->put($dataset, $ownerKey),
            'file_name' => $dataset->originalName,
            'format' => strtoupper($dataset->format),
            'headers' => $dataset->headers,
            'preview_rows' => array_slice($dataset->rows, 0, 10),
            'total_rows' => count($dataset->rows),
            'suggested_mapping' => $this->suggestions($dataset->headers),
        ];
    }

    /** @param list<string> $headers */
    private function suggestions(array $headers): array
    {
        $aliases = [
            'name_column' => ['nome', 'funcionario', 'funcionário'],
            'department_column' => ['departamento', 'setor'],
            'role_column' => ['cargo', 'funcao', 'função'],
            'salary_column' => ['salario', 'salário'],
            'variable_pay_column' => ['variavel', 'variável', 'media variavel', 'média variável'],
            'benefits_column' => ['beneficios', 'benefícios'],
            'regime_column' => ['regime'],
            'rat_column' => ['rat'],
            'third_parties_column' => ['terceiros'],
            'monthly_hours_column' => ['horas mensais', 'jornada mensal'],
        ];

        $normalizedHeaders = collect($headers)->mapWithKeys(
            static fn (string $header): array => [Str::slug($header) => $header],
        );
        $result = [];
        foreach ($aliases as $field => $options) {
            foreach ($options as $option) {
                $match = $normalizedHeaders->get(Str::slug($option));
                if ($match !== null) {
                    $result[$field] = $match;
                    break;
                }
            }
        }

        return $result;
    }
}
