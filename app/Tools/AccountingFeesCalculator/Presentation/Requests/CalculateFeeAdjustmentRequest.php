<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CalculateFeeAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_name' => ['required', 'string', 'max:150'],
            'index_type' => ['required', Rule::in(['ipca', 'inpc', 'igpm', 'manual'])],
            'reference_period' => ['required', 'date_format:Y-m'],
            'current_value' => ['required', 'string', 'regex:/^\s*(?:R\$\s*)?\d{1,3}(?:\.\d{3})*(?:,\d{1,2})?\s*$|^\s*(?:R\$\s*)?\d+(?:,\d{1,2})?\s*$/'],
            'percentage' => ['required', 'numeric', 'between:-100,1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'current_value.regex' => 'Informe um valor monetário válido em BRL.',
            'reference_period.date_format' => 'Informe a competência no formato mês/ano.',
        ];
    }
}
