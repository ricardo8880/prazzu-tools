<?php

declare(strict_types=1);

namespace App\Tools\DigitalCertificateAnalyzer\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

final class ExecuteToolRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'certificate_file' => ['required', 'file', 'max:5120'],
            'password' => ['required', 'string', 'max:512'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $file = $this->file('certificate_file');
            if ($file === null) return;
            $extension = strtolower($file->getClientOriginalExtension());
            if (! in_array($extension, ['pfx', 'p12'], true)) {
                $validator->errors()->add('certificate_file', 'Envie um certificado A1 com extensão .pfx ou .p12.');
            }
        });
    }

    public function attributes(): array
    {
        return ['certificate_file' => 'arquivo do certificado', 'password' => 'senha do certificado'];
    }
}
