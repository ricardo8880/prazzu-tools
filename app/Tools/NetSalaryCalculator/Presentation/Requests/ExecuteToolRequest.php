<?php

declare(strict_types=1);

namespace App\Tools\NetSalaryCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ExecuteToolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'competence' => ['required', 'regex:/^2026-(?:0[1-9]|1[0-2])$/'],
            'base_salary' => ['required', 'brazilian_money', 'money_min:0.01'],
            'taxable_additional_earnings' => ['nullable', 'brazilian_money', 'money_min:0'],
            'non_taxable_earnings' => ['nullable', 'brazilian_money', 'money_min:0'],
            'dependents' => ['nullable', 'integer', 'min:0', 'max:99'],
            'judicial_pension' => ['nullable', 'brazilian_money', 'money_min:0'],
            'transport_discount' => ['nullable', 'brazilian_money', 'money_min:0'],
            'meal_discount' => ['nullable', 'brazilian_money', 'money_min:0'],
            'health_plan_discount' => ['nullable', 'brazilian_money', 'money_min:0'],
            'other_discounts' => ['nullable', 'brazilian_money', 'money_min:0'],
            'confirm_assumptions' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'competence.regex' => 'A versão normativa disponível nesta ferramenta cobre competências de 2026.',
            'confirm_assumptions.accepted' => 'Confirme as premissas do cálculo antes de continuar.',
        ];
    }
}
