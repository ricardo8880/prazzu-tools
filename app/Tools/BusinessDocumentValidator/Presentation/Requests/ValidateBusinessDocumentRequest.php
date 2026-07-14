<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Presentation\Requests;

use App\Tools\BusinessDocumentValidator\Domain\Enums\DocumentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ValidateBusinessDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'document_type' => ['required', Rule::enum(DocumentType::class)],
            'document_number' => ['required', 'string', 'max:30', 'regex:/^[0-9.\-\/\s]+$/'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'document_type.required' => 'Selecione como o tipo do documento deve ser identificado.',
            'document_type.enum' => 'O tipo de documento selecionado é inválido.',
            'document_number.required' => 'Informe um CPF ou CNPJ para validar.',
            'document_number.max' => 'O documento informado é muito longo.',
            'document_number.regex' => 'Use somente números e os caracteres de máscara ponto, traço ou barra.',
        ];
    }
}
