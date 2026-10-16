<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CalculateFeeAdjustmentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'scenario_label' => ['required', 'string', 'max:150'],
            'index_type' => ['required', Rule::in(['cpi_u', 'manual'])],
            'reference_period' => ['required', 'date_format:Y-m'],
            'current_value' => ['required', 'us_money', 'money_min:0.01'],
            'percentage' => ['required', 'numeric', 'between:-100,1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return ['reference_period.date_format' => 'Enter the reference period as year-month.'];
    }
}
