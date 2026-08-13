<?php

declare(strict_types=1);

namespace App\Tools\MeiToMicroenterpriseSimulator\Presentation\Requests;

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
            'current_annual_revenue' => ['required', 'brazilian_money', 'money_min:0'],
            'projected_annual_revenue' => ['required', 'brazilian_money', 'money_min:0.01'],
            'me_effective_tax_rate' => ['nullable', 'brazilian_percentage', 'percentage_min:0', 'percentage_max:100'],
            'monthly_accounting_cost' => ['nullable', 'brazilian_money', 'money_min:0'],
            'monthly_other_cost' => ['nullable', 'brazilian_money', 'money_min:0'],
            'monthly_mei_cost' => ['nullable', 'brazilian_money', 'money_min:0'],
            'annual_growth_rate' => ['nullable', 'brazilian_percentage', 'percentage_min:0', 'percentage_max:500'],
            'projection_years' => ['nullable', 'integer', 'min:1', 'max:10'],
            'target_fixed_cost_burden' => ['nullable', 'brazilian_percentage', 'percentage_min:0.01', 'percentage_max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'current_annual_revenue.required' => 'Informe o faturamento anual atual.',
            'projected_annual_revenue.required' => 'Informe o faturamento anual projetado.',
        ];
    }
}
