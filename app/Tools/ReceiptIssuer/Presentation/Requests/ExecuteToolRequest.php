<?php

declare(strict_types=1);

namespace App\Tools\ReceiptIssuer\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ExecuteToolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'number' => ['required', 'string', 'max:40', 'regex:/^[A-Za-z0-9.\/_-]+$/'],
            'payer_name' => ['required', 'string', 'min:2', 'max:160'],
            'payer_document_type' => ['nullable', Rule::in(['ein', 'id']), 'required_with:payer_document'],
            'payer_document' => ['nullable', 'string', 'max:40', 'required_with:payer_document_type'],
            'payee_name' => ['required', 'string', 'min:2', 'max:160'],
            'payee_document_type' => ['nullable', Rule::in(['ein', 'id']), 'required_with:payee_document'],
            'payee_document' => ['nullable', 'string', 'max:40', 'required_with:payee_document_type'],
            'amount' => ['required', 'us_money', 'money_min:0.01'],
            'description' => ['required', 'string', 'min:3', 'max:1000'],
            'issued_at' => ['required', 'date_format:Y-m-d'],
            'city' => ['nullable', 'string', 'max:120'],
            'brand_name' => ['nullable', 'string', 'max:120'],
            'brand_document' => ['nullable', 'string', 'max:40'],
            'brand_footer' => ['nullable', 'string', 'max:160'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'number.regex' => 'Use letters, numbers, periods, slashes, underscores, or hyphens in the receipt number.',
            'amount.regex' => 'Enter a valid U.S. dollar amount, for example 1,000.00.',
            'payer_document_type.required_with' => 'Select the payer ID type.',
            'payer_document.required_with' => 'Enter the payer ID.',
            'payee_document_type.required_with' => 'Select the payee ID type.',
            'payee_document.required_with' => 'Enter the payee ID.',
        ];
    }

    protected function prepareForValidation(): void
    {
        foreach (['number', 'payer_name', 'payer_document', 'payee_name', 'payee_document', 'amount', 'description', 'city', 'brand_name', 'brand_document', 'brand_footer'] as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $this->merge([$field => trim((string) $this->input($field))]);
            }
        }
    }
}
