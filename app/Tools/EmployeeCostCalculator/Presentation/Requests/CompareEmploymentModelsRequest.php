<?php

declare(strict_types=1);

namespace App\Tools\EmployeeCostCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CompareEmploymentModelsRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'salary' => ['required', 'us_money', 'money_min:0.01'],
            'variable_pay' => ['required', 'us_money', 'money_min:0'],
            'benefits' => ['required', 'us_money', 'money_min:0'],
            'futa_profile' => ['required', 'in:standard,futa_exempt'],
            'suta_rate' => ['required', 'numeric', 'min:0', 'max:15'],
            'other_employer_cost_rate' => ['required', 'numeric', 'min:0', 'max:25'],
            'monthly_hours' => ['required', 'integer', 'min:1', 'max:744'],
            'w2_employee_deduction_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'contractor_monthly_payment' => ['required', 'us_money', 'money_min:0'],
            'contractor_tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'contractor_expenses' => ['required', 'us_money', 'money_min:0'],
            'self_employed_gross' => ['required', 'us_money', 'money_min:0'],
            'self_employed_tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'self_employed_payer_overhead_rate' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }
}
