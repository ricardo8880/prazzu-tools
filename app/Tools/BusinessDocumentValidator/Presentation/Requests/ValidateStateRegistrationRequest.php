<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ValidateStateRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'state' => ['required', 'string', Rule::in(['AUTO', 'CE', 'ES', 'MA', 'MG', 'PA', 'PB', 'PE', 'PR', 'RJ', 'RS', 'SC', 'SE', 'SP'])],
            'state_registration' => ['required', 'string', 'max:30'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'state.required' => 'Selecione a UF ou a detecção por candidatos.',
            'state.in' => 'A UF selecionada ainda não possui regra disponível.',
            'state_registration.required' => 'Informe a Inscrição Estadual.',
            'state_registration.max' => 'A Inscrição Estadual deve ter no máximo 30 caracteres.',
        ];
    }
}
