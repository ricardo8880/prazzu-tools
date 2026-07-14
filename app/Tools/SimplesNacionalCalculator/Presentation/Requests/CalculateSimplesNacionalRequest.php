<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CalculateSimplesNacionalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'annex' => ['required_unless:use_factor_r,1', Rule::in(['I', 'II', 'III', 'IV', 'V'])],
            'use_factor_r' => ['nullable', 'boolean'],
            'rbt12' => ['required', 'regex:/^\s*(?:R\$\s*)?\d{1,12}(?:\.\d{3})*(?:,\d{1,2})?\s*$/'],
            'monthly_revenue' => ['required', 'regex:/^\s*(?:R\$\s*)?\d{1,12}(?:\.\d{3})*(?:,\d{1,2})?\s*$/'],
            'payroll_12' => ['required_if:use_factor_r,1', 'nullable', 'regex:/^\s*(?:R\$\s*)?\d{1,12}(?:\.\d{3})*(?:,\d{1,2})?\s*$/'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'annex.required_unless' => 'Selecione o anexo ou ative o cálculo pelo Fator R.',
            'annex.in' => 'Selecione um anexo válido.',
            'rbt12.regex' => 'Informe o RBT12 no formato 1.234,56.',
            'monthly_revenue.regex' => 'Informe o faturamento mensal no formato 1.234,56.',
            'payroll_12.required_if' => 'Informe a folha de salários dos últimos 12 meses para calcular o Fator R.',
            'payroll_12.regex' => 'Informe a folha de salários no formato 1.234,56.',
        ];
    }
}
