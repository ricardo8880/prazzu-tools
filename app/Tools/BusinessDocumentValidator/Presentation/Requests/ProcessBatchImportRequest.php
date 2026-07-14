<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ProcessBatchImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'import_token' => ['required', 'string', 'size:48'],
            'document_column' => ['required', 'string', 'max:255'],
            'legal_name_column' => ['nullable', 'string', 'max:255'],
            'trade_name_column' => ['nullable', 'string', 'max:255'],
            'state_column' => ['nullable', 'string', 'max:255'],
            'city_column' => ['nullable', 'string', 'max:255'],
            'state_registration_column' => ['nullable', 'string', 'max:255'],
            'consult_registry' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'consult_registry' => $this->boolean('consult_registry'),
        ]);
    }

    public function messages(): array
    {
        return [
            'import_token.required' => 'A pré-visualização expirou. Importe o arquivo novamente.',
            'document_column.required' => 'Selecione a coluna que contém CPF ou CNPJ.',
        ];
    }
}
