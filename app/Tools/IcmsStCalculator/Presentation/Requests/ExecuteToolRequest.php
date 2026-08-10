<?php

declare(strict_types=1);

namespace App\Tools\IcmsStCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ExecuteToolRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $ufs='AC,AL,AP,AM,BA,CE,DF,ES,GO,MA,MT,MS,MG,PA,PB,PR,PE,PI,RJ,RN,RS,RO,RR,SC,SP,SE,TO';
        return [
            'competence'=>['required','regex:/^2026-(?:0[1-9]|1[0-2])$/'],
            'operation_type'=>['required','in:internal,interstate'],'origin_uf'=>['required','in:'.$ufs],'destination_uf'=>['required','in:'.$ufs],
            'merchandise_value'=>['required','brazilian_money','money_min:0.01'],'freight'=>['required','brazilian_money','money_min:0'],'insurance'=>['required','brazilian_money','money_min:0'],
            'other_charges'=>['required','brazilian_money','money_min:0'],'ipi'=>['required','brazilian_money','money_min:0'],'discount'=>['required','brazilian_money','money_min:0'],
            'original_mva'=>['required','numeric','min:0','max:500'],'internal_rate'=>['required','numeric','gt:0','lt:100'],'interstate_rate'=>['nullable','numeric','min:0','lt:100'],
            'adjust_mva'=>['nullable','boolean'],'fcp_rate'=>['nullable','numeric','min:0','max:10'],'own_icms_override'=>['nullable','brazilian_money','money_min:0'],
            'items'=>['nullable','array','max:10'],'items.*.description'=>['nullable','string','max:120'],'items.*.merchandise_value'=>['nullable','brazilian_money','money_min:0'],'items.*.mva'=>['nullable','numeric','min:0','max:500'],
            'confirm_scope'=>['accepted'],
        ];
    }
    public function withValidator($validator): void
    {
        $validator->after(function($validator): void {
            if ($this->input('operation_type') === 'interstate') {
                if ($this->input('origin_uf') === $this->input('destination_uf')) $validator->errors()->add('destination_uf','Em operação interestadual, origem e destino devem ser diferentes.');
                if ($this->input('interstate_rate') === null || $this->input('interstate_rate') === '') $validator->errors()->add('interstate_rate','Informe a alíquota interestadual da operação.');
            }
        });
    }
    public function messages(): array
    {
        return ['competence.regex'=>'A versão normativa desta ferramenta cobre competências de 2026.','confirm_scope.accepted'=>'Confirme que verificou NCM/CEST, MVA, alíquotas, FCP e enquadramento da operação.'];
    }
}
