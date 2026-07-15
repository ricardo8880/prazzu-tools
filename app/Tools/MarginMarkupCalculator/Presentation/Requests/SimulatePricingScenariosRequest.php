<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class SimulatePricingScenariosRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        $money = ['nullable', 'regex:/^\s*(?:R\$\s*)?\d{1,8}(?:\.\d{3})*(?:,\d{1,2})?\s*$/'];
        $percentage = ['nullable', 'numeric', 'min:0', 'lt:100'];

        return [
            'reference_date' => ['required', 'date_format:Y-m-d'],
            'product_name' => ['required', 'string', 'max:120'],
            'base_cost' => ['required', 'regex:/^\s*(?:R\$\s*)?\d{1,8}(?:\.\d{3})*(?:,\d{1,2})?\s*$/'],
            'additional_costs' => $money,
            'freight_cost' => $money,
            'packaging_cost' => $money,
            'fixed_expenses' => $money,
            'scenarios' => ['required', 'array', 'min:2', 'max:6'],
            'scenarios.*.name' => ['required', 'string', 'max:60'],
            'scenarios.*.cost_adjustment' => ['nullable', 'numeric', 'min:-90', 'max:500'],
            'scenarios.*.desired_margin' => ['required', 'numeric', 'min:0', 'lt:100'],
            'scenarios.*.discount_percentage' => $percentage,
            'scenarios.*.taxes_percentage' => $percentage,
            'scenarios.*.commission_percentage' => $percentage,
            'scenarios.*.card_fees_percentage' => $percentage,
            'scenarios.*.marketplace_fees_percentage' => $percentage,
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'product_name.required' => 'Informe o produto ou serviço simulado.',
            'base_cost.required' => 'Informe o custo base da simulação.',
            'base_cost.regex' => 'Use o formato 1.234,56 no custo base.',
            'scenarios.min' => 'Informe pelo menos dois cenários para comparação.',
            'scenarios.max' => 'Compare no máximo seis cenários por vez.',
            'scenarios.*.name.required' => 'Informe o nome de todos os cenários.',
            'scenarios.*.desired_margin.required' => 'Informe a margem desejada de todos os cenários.',
        ];
    }
}
