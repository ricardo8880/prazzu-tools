<?php

declare(strict_types=1);

namespace App\Tools\DifalIcmsCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ExecuteToolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $ufs = 'AC,AL,AP,AM,BA,CE,DF,ES,GO,MA,MT,MS,MG,PA,PB,PR,PE,PI,RJ,RN,RS,RO,RR,SC,SP,SE,TO';

        return ['competence' => ['required', 'regex:/^2026-(?:0[1-9]|1[0-2])$/'], 'base' => ['required', 'brazilian_money', 'money_min:0.01'], 'origin_uf' => ['required', 'in:'.$ufs], 'destination_uf' => ['required', 'different:origin_uf', 'in:'.$ufs], 'imported' => ['nullable', 'boolean'], 'interstate_rate' => ['nullable', 'numeric', 'min:0', 'max:100'], 'internal_rate' => ['required', 'numeric', 'gt:0', 'lt:100'], 'fcp_rate' => ['nullable', 'numeric', 'min:0', 'max:10'], 'method' => ['required', 'in:single_base,double_base'], 'recipient_taxpayer' => ['nullable', 'boolean'], 'confirm_rates' => ['accepted']];
    }

    public function messages(): array
    {
        return ['competence.regex' => 'A versão normativa deste lote cobre competências de 2026.', 'destination_uf.different' => 'Origem e destino devem ser UFs diferentes.', 'confirm_rates.accepted' => 'Confirme que verificou alíquota interna, FCP e método de base aplicáveis ao caso.'];
    }
}
