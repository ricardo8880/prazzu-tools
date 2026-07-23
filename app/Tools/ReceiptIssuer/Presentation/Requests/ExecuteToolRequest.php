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
            'payer_document_type' => ['nullable', Rule::in(['cpf', 'cnpj']), 'required_with:payer_document'],
            'payer_document' => ['nullable', 'string', 'max:18', 'required_with:payer_document_type'],
            'payee_name' => ['required', 'string', 'min:2', 'max:160'],
            'payee_document_type' => ['nullable', Rule::in(['cpf', 'cnpj']), 'required_with:payee_document'],
            'payee_document' => ['nullable', 'string', 'max:18', 'required_with:payee_document_type'],
            'amount' => ['required', 'regex:/^\s*(?:R\$\s*)?\d{1,9}(?:\.\d{3})*(?:,\d{1,2})?\s*$/'],
            'description' => ['required', 'string', 'min:3', 'max:1000'],
            'issued_at' => ['required', 'date_format:Y-m-d'],
            'city' => ['nullable', 'string', 'max:120'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'number.regex' => 'Use apenas letras, números e os separadores ponto, barra, sublinhado ou hífen no número do recibo.',
            'amount.regex' => 'Informe o valor no formato 1.000,00.',
            'payer_document_type.required_with' => 'Selecione o tipo do documento do pagador.',
            'payer_document.required_with' => 'Informe o documento do pagador.',
            'payee_document_type.required_with' => 'Selecione o tipo do documento do recebedor.',
            'payee_document.required_with' => 'Informe o documento do recebedor.',
        ];
    }

    protected function prepareForValidation(): void
    {
        foreach (['number', 'payer_name', 'payer_document', 'payee_name', 'payee_document', 'amount', 'description', 'city'] as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $this->merge([$field => trim((string) $this->input($field))]);
            }
        }
    }
}
