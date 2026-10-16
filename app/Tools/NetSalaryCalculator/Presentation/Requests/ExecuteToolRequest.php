<?php

declare(strict_types=1);

namespace App\Tools\NetSalaryCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ExecuteToolRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'competence' => ['required', 'regex:/^2026-(?:0[1-9]|1[0-2])$/'],
            'base_salary' => ['required', 'us_money', 'money_min:0.01'],
            'taxable_additional_earnings' => ['nullable', 'us_money', 'money_min:0'],
            'non_taxable_earnings' => ['nullable', 'us_money', 'money_min:0'],
            'filing_status' => ['required', 'in:single,married_joint,married_separate,head_household'],
            'pretax_deductions' => ['nullable', 'us_money', 'money_min:0'],
            'state_local_withholding' => ['nullable', 'us_money', 'money_min:0'],
            'additional_federal_withholding' => ['nullable', 'us_money', 'money_min:0'],
            'benefit_deductions' => ['nullable', 'us_money', 'money_min:0'],
            'other_deductions' => ['nullable', 'us_money', 'money_min:0'],
            'confirm_assumptions' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'competence.regex' => 'This version covers pay periods in calendar year 2026.',
            'confirm_assumptions.accepted' => 'Confirm the assumptions before calculating.',
        ];
    }
}
