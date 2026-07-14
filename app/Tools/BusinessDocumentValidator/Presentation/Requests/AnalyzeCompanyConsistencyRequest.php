<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class AnalyzeCompanyConsistencyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'analysis_cnpj' => ['required', 'string', 'max:30', 'regex:/^[0-9.\/\-\s]+$/'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'trade_name' => ['nullable', 'string', 'max:255'],
            'analysis_state' => ['nullable', 'required_with:analysis_state_registration', 'string', Rule::in([
                'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG',
                'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO',
            ])],
            'city' => ['nullable', 'string', 'max:150'],
            'analysis_state_registration' => ['nullable', 'string', 'max:30', 'regex:/^[0-9.\/\-\sPp]+$/'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'analysis_cnpj.required' => 'Informe o CNPJ que será analisado.',
            'analysis_cnpj.regex' => 'O CNPJ deve conter somente números e caracteres de máscara.',
            'analysis_state.required_with' => 'Selecione a UF para validar a Inscrição Estadual.',
            'analysis_state_registration.regex' => 'A Inscrição Estadual contém caracteres não permitidos.',
        ];
    }
}
