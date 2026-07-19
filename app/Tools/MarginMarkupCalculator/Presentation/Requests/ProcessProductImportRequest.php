<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ProcessProductImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $headers = array_values((array) $this->input('available_headers', []));
        $optional = ['nullable', 'string', Rule::in($headers)];

        return [
            'import_token' => ['required', 'string', 'size:48'],
            'available_headers' => ['required', 'array', 'min:1'],
            'available_headers.*' => ['required', 'string'],
            'name_column' => ['required', 'string', Rule::in($headers)],
            'base_cost_column' => ['required', 'string', Rule::in($headers)],
            'code_column' => $optional,
            'category_column' => $optional,
            'additional_costs_column' => $optional,
            'freight_cost_column' => $optional,
            'packaging_cost_column' => $optional,
            'fixed_expenses_column' => $optional,
            'desired_margin_column' => $optional,
            'taxes_percentage_column' => $optional,
            'commission_percentage_column' => $optional,
            'card_fees_percentage_column' => $optional,
            'marketplace_fees_percentage_column' => $optional,
        ];
    }
}
