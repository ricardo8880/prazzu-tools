<?php

declare(strict_types=1);

namespace App\Tools\ReceiptIssuer\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class BatchIssueRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return ['file' => ['required', 'file', 'mimes:csv,txt', 'max:2048']];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'file.required' => 'Selecione o arquivo CSV com os recibos.',
            'file.mimes' => 'Envie um arquivo CSV ou TXT delimitado por ponto e vírgula ou vírgula.',
            'file.max' => 'O arquivo deve ter no máximo 2 MB.',
        ];
    }
}
