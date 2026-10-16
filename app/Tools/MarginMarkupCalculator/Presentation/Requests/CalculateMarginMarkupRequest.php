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
        $money = ['nullable', 'us_money', 'money_min:0'];
        $percentage = ['nullable', 'numeric', 'min:0', 'lt:100'];

        return [
            'reference_date' => ['required', 'date_format:Y-m-d'],
            'base_cost' => ['required', 'us_money', 'money_min:0.01'],
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
            'base_cost.us_money' => 'Enter the base cost in U.S. format, for example 1,234.56.',
            '*.us_money' => 'Enter monetary values in U.S. format, for example 1,234.56.',
            '*.min' => 'Percentage values cannot be negative.',
            '*.lt' => 'Each percentage must be below 100%.',
        ];
    }
}
