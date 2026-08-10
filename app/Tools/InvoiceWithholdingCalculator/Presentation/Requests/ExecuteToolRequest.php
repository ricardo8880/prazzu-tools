<?php

declare(strict_types=1);

namespace App\Tools\InvoiceWithholdingCalculator\Presentation\Requests;

use App\Core\Money\Money;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Throwable;

final class ExecuteToolRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $percentage=['required','numeric','min:0','max:100'];
        return [
            'competence'=>['required','date_format:Y-m','after_or_equal:2026-01','before_or_equal:2026-12'],
            'invoice_number'=>['nullable','string','max:40'],'service_description'=>['required','string','max:120'],'gross_value'=>['required','brazilian_money','money_min:0.01'],
            'apply_irrf'=>['nullable','boolean'],'irrf_rate'=>$percentage,'irrf_base_percent'=>$percentage,
            'apply_inss'=>['nullable','boolean'],'inss_rate'=>$percentage,'inss_base_percent'=>$percentage,
            'apply_iss'=>['nullable','boolean'],'iss_rate'=>$percentage,'iss_base_percent'=>$percentage,
            'apply_pis'=>['nullable','boolean'],'pis_rate'=>$percentage,'pis_base_percent'=>$percentage,
            'apply_cofins'=>['nullable','boolean'],'cofins_rate'=>$percentage,'cofins_base_percent'=>$percentage,
            'apply_csll'=>['nullable','boolean'],'csll_rate'=>$percentage,'csll_base_percent'=>$percentage,
            'notes'=>['nullable','array','max:20'],'notes.*.description'=>['nullable','string','max:120'],'notes.*.value'=>['nullable','brazilian_money','money_min:0'],
            'confirm_scope'=>['accepted'],
        ];
    }
    public function withValidator(Validator $validator): void
    {
        $validator->after(function(Validator $validator): void {
            if($validator->errors()->isNotEmpty()) return;
            $enabled=false; foreach(['irrf','inss','iss','pis','cofins','csll'] as $tax){ if($this->boolean('apply_'.$tax)){ $enabled=true; break; } }
            if(!$enabled) $validator->errors()->add('apply_irrf','Selecione ao menos uma retenção para calcular.');
            try{
                $total=Money::fromDecimal((string)$this->input('gross_value','0'));
                foreach((array)$this->input('notes',[]) as $note){ $v=trim((string)($note['value']??'')); if($v!=='') $total=$total->add(Money::fromDecimal($v)); }
                if($total->minorAmount()<=0) $validator->errors()->add('gross_value','Informe valor bruto total maior que zero.');
            }catch(Throwable){}
        });
    }
    public function messages(): array
    {
        return ['confirm_scope.accepted'=>'Confirme que revisou a aplicabilidade, bases e alíquotas de cada retenção.','competence.after_or_equal'=>'Informe uma competência de 2026.','competence.before_or_equal'=>'Informe uma competência de 2026.'];
    }
}
