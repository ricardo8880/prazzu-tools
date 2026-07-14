<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class PreviewBatchImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'batch_file' => ['required', 'file', 'max:5120', 'mimes:csv,txt,xlsx'],
        ];
    }

    public function messages(): array
    {
        return [
            'batch_file.required' => 'Selecione um arquivo para importar.',
            'batch_file.file' => 'O arquivo enviado é inválido.',
            'batch_file.max' => 'O arquivo deve possuir no máximo 5 MB.',
            'batch_file.mimes' => 'Envie uma planilha CSV ou XLSX.',
        ];
    }
}
