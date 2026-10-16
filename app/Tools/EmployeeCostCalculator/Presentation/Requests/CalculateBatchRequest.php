<?php

declare(strict_types=1);

namespace App\Tools\EmployeeCostCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CalculateBatchRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'scenario_name' => ['nullable', 'string', 'max:120'],
            'company_profile_id' => ['nullable', 'integer'],
            'employees' => ['required', 'array', 'min:1', 'max:500'],
            'employees.*.employee_name' => ['required', 'string', 'max:160'],
            'employees.*.department' => ['nullable', 'string', 'max:120'],
            'employees.*.role' => ['nullable', 'string', 'max:120'],
            'employees.*.salary' => ['required', 'us_money', 'money_min:0.01'],
            'employees.*.variable_pay' => ['required', 'us_money', 'money_min:0'],
            'employees.*.benefits' => ['required', 'us_money', 'money_min:0'],
            'employees.*.futa_profile' => ['required', 'in:standard,futa_exempt'],
            'employees.*.suta_rate' => ['required', 'numeric', 'min:0', 'max:15'],
            'employees.*.other_employer_cost_rate' => ['required', 'numeric', 'min:0', 'max:25'],
            'employees.*.monthly_hours' => ['required', 'integer', 'min:1', 'max:744'],
        ];
    }
}
