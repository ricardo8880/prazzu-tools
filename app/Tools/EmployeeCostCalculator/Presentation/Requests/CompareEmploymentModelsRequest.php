<?php

declare(strict_types=1);

namespace App\Tools\EmployeeCostCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CompareEmploymentModelsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'salary' => ['required', 'brazilian_money', 'money_min:0.01'],
            'variable_pay' => ['required', 'brazilian_money', 'money_min:0'],
            'benefits' => ['required', 'brazilian_money', 'money_min:0'],
            'regime' => ['required', 'in:general,simples_annex_iv,simples_other'],
            'rat' => ['required', 'numeric', 'min:0', 'max:15'],
            'third_parties' => ['required', 'numeric', 'min:0', 'max:15'],
            'monthly_hours' => ['required', 'integer', 'min:1', 'max:744'],
            'clt_employee_discount_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'pj_monthly_invoice' => ['required', 'brazilian_money', 'money_min:0'],
            'pj_tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'pj_expenses' => ['required', 'brazilian_money', 'money_min:0'],
            'autonomous_gross' => ['required', 'brazilian_money', 'money_min:0'],
            'autonomous_discount_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'autonomous_employer_rate' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }
}
