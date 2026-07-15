<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class PreviewProductImportRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return ['import_file' => ['required', 'file', 'mimes:csv,txt,xlsx', 'max:5120']];
    }

    public function messages(): array
    {
        return [
            'import_file.required' => 'Selecione um arquivo CSV ou XLSX.',
            'import_file.mimes' => 'Envie um arquivo CSV ou XLSX válido.',
            'import_file.max' => 'O arquivo deve possuir no máximo 5 MB.',
        ];
    }
}
