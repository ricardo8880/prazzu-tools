<?php

declare(strict_types=1);

namespace App\Tools\EmployeeCostCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CompareScenariosRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'scenarios' => ['required', 'array', 'min:2', 'max:6'],
            'scenarios.*.scenario_name' => ['required', 'string', 'max:120', 'distinct'],
            'scenarios.*.employees' => ['required', 'array', 'min:1', 'max:100'],
            'scenarios.*.employees.*.employee_name' => ['required', 'string', 'max:160'],
            'scenarios.*.employees.*.department' => ['nullable', 'string', 'max:120'],
            'scenarios.*.employees.*.role' => ['nullable', 'string', 'max:120'],
            'scenarios.*.employees.*.salary' => ['required', 'us_money', 'money_min:0.01'],
            'scenarios.*.employees.*.variable_pay' => ['required', 'us_money', 'money_min:0'],
            'scenarios.*.employees.*.benefits' => ['required', 'us_money', 'money_min:0'],
            'scenarios.*.employees.*.futa_profile' => ['required', 'in:standard,futa_exempt'],
            'scenarios.*.employees.*.suta_rate' => ['required', 'numeric', 'min:0', 'max:15'],
            'scenarios.*.employees.*.other_employer_cost_rate' => ['required', 'numeric', 'min:0', 'max:25'],
            'scenarios.*.employees.*.monthly_hours' => ['required', 'integer', 'min:1', 'max:744'],
        ];
    }
}
