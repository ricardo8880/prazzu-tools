<?php

declare(strict_types=1);

namespace App\Tools\ReceiptIssuer\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class BatchIssueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return ['file' => ['required', 'file', 'mimes:csv,txt', 'max:2048']];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'file.required' => 'Select the CSV file containing the receipts.',
            'file.mimes' => 'Upload a CSV or TXT file delimited by semicolons or commas.',
            'file.max' => 'The file must be no larger than 2 MB.',
        ];
    }
}
