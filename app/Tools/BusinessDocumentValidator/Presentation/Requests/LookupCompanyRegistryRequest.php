<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class LookupCompanyRegistryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, list<string>> */
    public function rules(): array
    {
        return [
            'cnpj' => ['required', 'string', 'max:30', 'regex:/^[0-9.\/\-\s]+$/'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'cnpj.required' => 'Informe o CNPJ que deseja consultar.',
            'cnpj.regex' => 'Use somente números e os caracteres da máscara do CNPJ.',
            'cnpj.max' => 'O CNPJ informado ultrapassa o tamanho permitido.',
        ];
    }
}
