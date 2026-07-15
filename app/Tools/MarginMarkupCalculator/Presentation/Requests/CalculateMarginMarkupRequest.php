<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CalculateMarginMarkupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        $money = ['nullable', 'regex:/^\s*(?:R\$\s*)?\d{1,8}(?:\.\d{3})*(?:,\d{1,2})?\s*$/'];
        $percentage = ['nullable', 'numeric', 'min:0', 'lt:100'];

        return [
            'reference_date' => ['required', 'date_format:Y-m-d'],
            'base_cost' => ['required', 'regex:/^\s*(?:R\$\s*)?\d{1,8}(?:\.\d{3})*(?:,\d{1,2})?\s*$/'],
            'additional_costs' => $money,
            'freight_cost' => $money,
            'packaging_cost' => $money,
            'fixed_expenses' => $money,
            'desired_margin' => ['required', 'numeric', 'min:0', 'lt:100'],
            'taxes_percentage' => $percentage,
            'commission_percentage' => $percentage,
            'card_fees_percentage' => $percentage,
            'marketplace_fees_percentage' => $percentage,
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'base_cost.regex' => 'Informe o custo base no formato 1.234,56.',
            '*.regex' => 'Informe os valores monetários no formato 1.234,56.',
            '*.min' => 'Os valores percentuais não podem ser negativos.',
            '*.lt' => 'Cada percentual precisa ser menor que 100%.',
        ];
    }
}
