<?php

namespace App\Tools\MarginMarkupCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CalculateMarginMarkupRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'reference_date' => ['required', 'date_format:Y-m-d'],
            'base_cost' => ['required', 'regex:/^\s*(?:R\$\s*)?\d{1,12}(?:\.\d{3})*(?:,\d{1,2})?\s*$/'],
            'additional_costs' => ['nullable', 'regex:/^\s*(?:R\$\s*)?\d{1,12}(?:\.\d{3})*(?:,\d{1,2})?\s*$/'],
            'desired_margin' => ['required', 'numeric', 'min:0', 'lt:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'base_cost.regex' => 'Informe o custo base no formato 1.234,56.',
            'additional_costs.regex' => 'Informe os custos adicionais no formato 1.234,56.',
            'desired_margin.lt' => 'A margem precisa ser menor que 100%.',
        ];
    }
}
