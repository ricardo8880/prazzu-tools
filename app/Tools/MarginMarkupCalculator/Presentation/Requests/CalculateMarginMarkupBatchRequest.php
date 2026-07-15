<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CalculateMarginMarkupBatchRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        $money = ['nullable', 'regex:/^\s*(?:R\$\s*)?\d{1,8}(?:\.\d{3})*(?:,\d{1,2})?\s*$/'];
        $percentage = ['nullable', 'numeric', 'min:0', 'lt:100'];

        return [
            'reference_date' => ['required', 'date_format:Y-m-d'],
            'products' => ['required', 'array', 'min:1', 'max:100'],
            'products.*.name' => ['required', 'string', 'max:120'],
            'products.*.code' => ['nullable', 'string', 'max:60'],
            'products.*.category' => ['nullable', 'string', 'max:80'],
            'products.*.base_cost' => ['required', 'regex:/^\s*(?:R\$\s*)?\d{1,8}(?:\.\d{3})*(?:,\d{1,2})?\s*$/'],
            'products.*.additional_costs' => $money,
            'products.*.freight_cost' => $money,
            'products.*.packaging_cost' => $money,
            'products.*.fixed_expenses' => $money,
            'products.*.desired_margin' => ['required', 'numeric', 'min:0', 'lt:100'],
            'products.*.taxes_percentage' => $percentage,
            'products.*.commission_percentage' => $percentage,
            'products.*.card_fees_percentage' => $percentage,
            'products.*.marketplace_fees_percentage' => $percentage,
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'products.required' => 'Adicione pelo menos um produto.',
            'products.max' => 'Calcule no máximo 100 produtos por vez.',
            'products.*.name.required' => 'Informe o nome de todos os produtos.',
            'products.*.base_cost.required' => 'Informe o custo base de todos os produtos.',
            'products.*.base_cost.regex' => 'Use o formato 1.234,56 nos custos base.',
            'products.*.*.regex' => 'Use o formato 1.234,56 nos valores monetários.',
        ];
    }
}
