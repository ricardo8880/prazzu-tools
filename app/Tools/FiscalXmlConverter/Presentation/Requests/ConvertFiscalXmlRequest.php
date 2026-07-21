<?php

declare(strict_types=1);

namespace App\Tools\FiscalXmlConverter\Presentation\Requests;

use App\Tools\FiscalXmlConverter\Domain\Services\NfeXmlParser;
use Illuminate\Foundation\Http\FormRequest;

final class ConvertFiscalXmlRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return ['xml_file' => ['required', 'file', 'max:'.(int) ceil(NfeXmlParser::MAX_XML_BYTES / 1024)]];
    }

    public function messages(): array
    {
        return [
            'xml_file.required' => 'Selecione um arquivo XML fiscal.',
            'xml_file.file' => 'O arquivo enviado não é válido.',
            'xml_file.max' => 'O XML deve possuir no máximo 10 MB.',
        ];
    }
}
