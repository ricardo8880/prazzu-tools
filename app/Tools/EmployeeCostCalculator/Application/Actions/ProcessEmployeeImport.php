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
            throw new RuntimeException('The import preview expired. Upload the file again.');
        }

        foreach ($this->mappingFields() as $mappingField) {
            $column = $data[$mappingField] ?? null;
            if ($column !== null && $column !== '' && ! in_array($column, $dataset->headers, true)) {
                throw new RuntimeException("The selected column for [{$mappingField}] is not present in the imported file.");
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
                'variable_pay' => $this->cell($row, $data['variable_pay_column'] ?? null, '0.00'),
                'benefits' => $this->cell($row, $data['benefits_column'] ?? null, '0.00'),
                'futa_profile' => $this->normalizeFutaProfile($this->cell($row, $data['futa_profile_column'] ?? null, 'standard')),
                'suta_rate' => $this->cell($row, $data['suta_rate_column'] ?? null, '0'),
                'other_employer_cost_rate' => $this->cell($row, $data['other_employer_cost_rate_column'] ?? null, '0'),
                'monthly_hours' => $this->cell($row, $data['monthly_hours_column'] ?? null, '173'),
            ];

            $validator = Validator::make($employee, [
                'employee_name' => ['required', 'string', 'max:160'],
                'department' => ['nullable', 'string', 'max:120'],
                'role' => ['nullable', 'string', 'max:120'],
                'salary' => ['required', 'us_money', 'money_min:0.01'],
                'variable_pay' => ['required', 'us_money', 'money_min:0'],
                'benefits' => ['required', 'us_money', 'money_min:0'],
                'futa_profile' => ['required', 'in:standard,futa_exempt'],
                'suta_rate' => ['required', 'numeric', 'min:0', 'max:15'],
                'other_employer_cost_rate' => ['required', 'numeric', 'min:0', 'max:25'],
                'monthly_hours' => ['required', 'integer', 'min:1', 'max:744'],
            ]);

            if ($validator->fails()) {
                $rejected[] = ['line' => $index + 2, 'errors' => $validator->errors()->all()];
                continue;
            }

            $employees[] = $validator->validated();
        }

        if ($employees === []) {
            throw new RuntimeException('No valid employee rows were found in the file.');
        }

        $this->store->forget((string) $data['import_token'], $ownerKey);

        return ['employees' => $employees, 'imported' => count($employees), 'rejected' => $rejected];
    }

    private function cell(array $row, mixed $column, string $default = ''): string
    {
        if (! is_string($column) || $column === '') return $default;
        $value = trim((string) ($row[$column] ?? ''));
        return $value === '' ? $default : $value;
    }

    private function normalizeFutaProfile(string $value): ?string
    {
        return match (strtolower(trim($value))) {
            'standard', 'taxable', 'futa taxable' => 'standard',
            'futa_exempt', 'futa exempt', 'exempt' => 'futa_exempt',
            default => null,
        };
    }

    /** @return list<string> */
    private function mappingFields(): array
    {
        return ['name_column', 'department_column', 'role_column', 'salary_column', 'variable_pay_column', 'benefits_column', 'futa_profile_column', 'suta_rate_column', 'other_employer_cost_rate_column', 'monthly_hours_column'];
    }
}
