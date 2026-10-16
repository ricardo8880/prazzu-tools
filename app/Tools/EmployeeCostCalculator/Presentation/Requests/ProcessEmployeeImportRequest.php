<?php

declare(strict_types=1);

namespace App\Tools\EmployeeCostCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ProcessEmployeeImportRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'import_token' => ['required', 'string', 'size:48'],
            'name_column' => ['required', 'string', 'max:160'],
            'department_column' => ['nullable', 'string', 'max:160'],
            'role_column' => ['nullable', 'string', 'max:160'],
            'salary_column' => ['required', 'string', 'max:160'],
            'variable_pay_column' => ['nullable', 'string', 'max:160'],
            'benefits_column' => ['nullable', 'string', 'max:160'],
            'futa_profile_column' => ['nullable', 'string', 'max:160'],
            'suta_rate_column' => ['nullable', 'string', 'max:160'],
            'other_employer_cost_rate_column' => ['nullable', 'string', 'max:160'],
            'monthly_hours_column' => ['nullable', 'string', 'max:160'],
        ];
    }
}
