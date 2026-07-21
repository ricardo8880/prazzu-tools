<?php

declare(strict_types=1);

namespace App\Tools\FiscalXmlConverter\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ConvertFiscalXmlBatchRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'xml_files' => ['required', 'array', 'min:2', 'max:50'],
            'xml_files.*' => ['required', 'file', 'max:10240', 'mimes:xml', 'mimetypes:text/xml,application/xml'],
        ];
    }

    public function messages(): array
    {
        return [
            'xml_files.required' => 'Selecione ao menos dois arquivos XML.',
            'xml_files.max' => 'Envie no máximo 50 arquivos por lote.',
            'xml_files.*.max' => 'Cada XML deve ter no máximo 10 MB.',
            'xml_files.*.mimes' => 'Todos os arquivos devem estar no formato XML.',
        ];
    }
}
