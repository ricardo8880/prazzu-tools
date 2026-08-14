<?php

declare(strict_types=1);

namespace App\Tools\EcadRoyaltySimulator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ExecuteToolRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $decimal = ['nullable', 'regex:/^\d{1,9}([\.,]\d{1,4})?$/'];
        return [
            'method' => ['required', Rule::in(['uda', 'uda_per_sqm', 'percentage'])],
            'uda_value' => ['required', 'brazilian_money', 'money_min:0.01'],
            'uda_quantity' => [...$decimal, 'required_if:method,uda'],
            'area_square_meters' => [...$decimal, 'required_if:method,uda_per_sqm'],
            'uda_per_square_meter' => [...$decimal, 'required_if:method,uda_per_sqm'],
            'reference_amount' => ['nullable', 'brazilian_money', 'money_min:0', 'required_if:method,percentage'],
            'percentage_rate' => ['nullable', 'brazilian_percentage', 'percentage_min:0', 'percentage_max:100', 'required_if:method,percentage'],
            'project_periods' => ['nullable', 'boolean'],
            'periods' => ['nullable', 'integer', 'min:1', 'max:60'],
        ];
    }
}
