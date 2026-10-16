<?php

declare(strict_types=1);

namespace App\Tools\EmployeeCostCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ExecuteToolRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'employee_name' => ['nullable', 'string', 'max:160'],
            'department' => ['nullable', 'string', 'max:120'],
            'scenario_name' => ['nullable', 'string', 'max:120'],
            'company_profile_id' => ['nullable', 'integer'],
            'salary' => ['required', 'us_money', 'money_min:0.01'],
            'variable_pay' => ['required', 'us_money', 'money_min:0'],
            'benefits' => ['required', 'us_money', 'money_min:0'],
            'futa_profile' => ['required', 'in:standard,futa_exempt'],
            'suta_rate' => ['required', 'numeric', 'min:0', 'max:15'],
            'other_employer_cost_rate' => ['required', 'numeric', 'min:0', 'max:25'],
            'monthly_hours' => ['required', 'integer', 'min:1', 'max:744'],
        ];
    }
}
