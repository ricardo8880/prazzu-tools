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
            'salary' => ['required', 'us_money', 'money_min:0.01'],
            'variable_pay' => ['required', 'us_money', 'money_min:0'],
            'benefits' => ['required', 'us_money', 'money_min:0'],
            'regime' => ['required', 'in:standard,futa_exempt'],
            'rat' => ['required', 'numeric', 'min:0', 'max:15'],
            'third_parties' => ['required', 'numeric', 'min:0', 'max:15'],
            'monthly_hours' => ['required', 'integer', 'min:1', 'max:744'],
            'clt_employee_discount_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'pj_monthly_invoice' => ['required', 'us_money', 'money_min:0'],
            'pj_tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'pj_expenses' => ['required', 'us_money', 'money_min:0'],
            'autonomous_gross' => ['required', 'us_money', 'money_min:0'],
            'autonomous_discount_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'autonomous_employer_rate' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }
}
