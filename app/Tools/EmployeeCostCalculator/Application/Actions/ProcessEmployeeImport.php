<?php

declare(strict_types=1);

namespace App\Tools\EmployeeCostCalculator\Application\Actions;

use App\Core\Imports\Contracts\ImportDatasetStore;
use Illuminate\Support\Facades\Validator;
use RuntimeException;

final readonly class ProcessEmployeeImport
{
    public function __construct(private ImportDatasetStore $store) {}

    /** @param array<string, mixed> $data */
    public function execute(array $data, string $ownerKey): array
    {
        $dataset = $this->store->get((string) $data['import_token'], $ownerKey);
        if ($dataset === null) {
            throw new RuntimeException('A pré-visualização expirou. Envie o arquivo novamente.');
        }

        foreach ($this->mappingFields() as $mappingField) {
            $column = $data[$mappingField] ?? null;
            if ($column !== null && $column !== '' && ! in_array($column, $dataset->headers, true)) {
                throw new RuntimeException("A coluna selecionada para [{$mappingField}] não pertence ao arquivo importado.");
            }
        }

        $employees = [];
        $rejected = [];
        foreach ($dataset->rows as $index => $row) {
            $employee = [
                'employee_name' => $this->cell($row, $data['name_column'] ?? null),
                'department' => $this->cell($row, $data['department_column'] ?? null),
                'role' => $this->cell($row, $data['role_column'] ?? null),
                'salary' => $this->cell($row, $data['salary_column'] ?? null),
                'variable_pay' => $this->cell($row, $data['variable_pay_column'] ?? null, '0,00'),
                'benefits' => $this->cell($row, $data['benefits_column'] ?? null, '0,00'),
                'regime' => $this->normalizeRegime($this->cell($row, $data['regime_column'] ?? null, 'general')),
                'rat' => $this->cell($row, $data['rat_column'] ?? null, '1'),
                'third_parties' => $this->cell($row, $data['third_parties_column'] ?? null, '5.8'),
                'monthly_hours' => $this->cell($row, $data['monthly_hours_column'] ?? null, '220'),
            ];

            $validator = Validator::make($employee, [
                'employee_name' => ['required', 'string', 'max:160'],
                'department' => ['nullable', 'string', 'max:120'],
                'role' => ['nullable', 'string', 'max:120'],
                'salary' => ['required', 'brazilian_money', 'money_min:0.01'],
                'variable_pay' => ['required', 'brazilian_money', 'money_min:0'],
                'benefits' => ['required', 'brazilian_money', 'money_min:0'],
                'regime' => ['required', 'in:general,simples_annex_iv,simples_other'],
                'rat' => ['required', 'numeric', 'min:0', 'max:15'],
                'third_parties' => ['required', 'numeric', 'min:0', 'max:15'],
                'monthly_hours' => ['required', 'integer', 'min:1', 'max:744'],
            ]);

            if ($validator->fails()) {
                $rejected[] = [
                    'line' => $index + 2,
                    'errors' => $validator->errors()->all(),
                ];

                continue;
            }

            $employees[] = $validator->validated();
        }

        if ($employees === []) {
            throw new RuntimeException('Nenhuma linha válida foi encontrada no arquivo.');
        }

        $this->store->forget((string) $data['import_token'], $ownerKey);

        return [
            'employees' => $employees,
            'imported' => count($employees),
            'rejected' => $rejected,
        ];
    }

    private function cell(array $row, mixed $column, string $default = ''): string
    {
        if (! is_string($column) || $column === '') {
            return $default;
        }

        $value = trim((string) ($row[$column] ?? ''));

        return $value === '' ? $default : $value;
    }

    private function normalizeRegime(string $value): ?string
    {
        return match (mb_strtolower(trim($value))) {
            'general', 'geral', 'regime geral' => 'general',
            'simples anexo iv', 'simples_annex_iv', 'anexo iv' => 'simples_annex_iv',
            'simples outros', 'simples_other', 'simples demais anexos' => 'simples_other',
            default => null,
        };
    }

    /** @return list<string> */
    private function mappingFields(): array
    {
        return [
            'name_column',
            'department_column',
            'role_column',
            'salary_column',
            'variable_pay_column',
            'benefits_column',
            'regime_column',
            'rat_column',
            'third_parties_column',
            'monthly_hours_column',
        ];
    }
}
