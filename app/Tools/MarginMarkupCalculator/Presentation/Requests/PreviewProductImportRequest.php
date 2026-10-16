<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class PreviewProductImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['import_file' => ['required', 'file', 'mimes:csv,txt,xlsx', 'max:5120']];
    }

    public function messages(): array
    {
        return [
            'import_file.required' => 'Select a CSV or XLSX file.',
            'import_file.mimes' => 'Upload a valid CSV or XLSX file.',
            'import_file.max' => 'The file must be no larger than 5 MB.',
        ];
    }
}
