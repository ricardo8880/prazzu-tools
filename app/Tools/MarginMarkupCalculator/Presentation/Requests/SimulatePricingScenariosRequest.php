<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class SimulatePricingScenariosRequest extends FormRequest
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
            'product_name' => ['required', 'string', 'max:120'],
            'base_cost' => ['required', 'us_money', 'money_min:0.01'],
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
            'product_name.required' => 'Enter the product or service being simulated.',
            'base_cost.required' => 'Enter the base cost for the simulation.',
            'base_cost.us_money' => 'Use U.S. money format for the base cost, for example 1,234.56.',
            'scenarios.min' => 'Enter at least two scenarios for comparison.',
            'scenarios.max' => 'Compare no more than six scenarios at a time.',
            'scenarios.*.name.required' => 'Enter a name for every scenario.',
            'scenarios.*.desired_margin.required' => 'Enter the desired margin for every scenario.',
        ];
    }
}
