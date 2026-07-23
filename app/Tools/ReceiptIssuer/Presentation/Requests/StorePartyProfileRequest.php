<?php

declare(strict_types=1);

namespace App\Tools\ReceiptIssuer\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StorePartyProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'party_type' => ['required', Rule::in(['payer', 'payee'])],
            'label' => ['required', 'string', 'min:2', 'max:80'],
            'name' => ['required', 'string', 'min:2', 'max:160'],
            'document_type' => ['nullable', Rule::in(['cpf', 'cnpj']), 'required_with:document'],
            'document' => ['nullable', 'string', 'max:18', 'required_with:document_type'],
        ];
    }

    protected function prepareForValidation(): void
    {
        foreach (['label', 'name', 'document'] as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $this->merge([$field => trim((string) $this->input($field))]);
            }
        }
    }
}
