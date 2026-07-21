<?php

declare(strict_types=1);

namespace App\Tools\VacationCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class PlanVacationsRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    /** @return array<string,mixed> */
    public function rules(): array
    {
        return [
            'employees' => ['required', 'array', 'min:1', 'max:50'],
            'employees.*.name' => ['required', 'string', 'max:120'],
            'employees.*.monthly_salary' => ['required', 'numeric', 'min:0.01'],
            'employees.*.acquisition_start_date' => ['required', 'date'],
            'employees.*.vacation_start_date' => ['required', 'date'],
            'employees.*.unjustified_absences' => ['nullable', 'integer', 'min:0', 'max:99'],
            'employees.*.convert_one_third_to_cash' => ['nullable', 'boolean'],
        ];
    }
}
