<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CalculateMarginMarkupBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        $money = ['nullable', 'us_money', 'money_min:0'];
        $percentage = ['nullable', 'numeric', 'min:0', 'lt:100'];

        return [
            'reference_date' => ['required', 'date_format:Y-m-d'],
            'products' => ['required', 'array', 'min:1', 'max:100'],
            'products.*.name' => ['required', 'string', 'max:120'],
            'products.*.code' => ['nullable', 'string', 'max:60'],
            'products.*.category' => ['nullable', 'string', 'max:80'],
            'products.*.base_cost' => ['required', 'us_money', 'money_min:0.01'],
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
            'products.required' => 'Add at least one product.',
            'products.max' => 'Calculate no more than 100 products at a time.',
            'products.*.name.required' => 'Enter a name for every product.',
            'products.*.base_cost.required' => 'Enter a base cost for every product.',
            'products.*.base_cost.us_money' => 'Use U.S. money format for base costs, for example 1,234.56.',
            'products.*.*.us_money' => 'Use U.S. money format for monetary values, for example 1,234.56.',
        ];
    }
}
