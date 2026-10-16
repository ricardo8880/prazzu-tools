<?php

declare(strict_types=1);

namespace App\Tools\IncomeStatementGenerator\Presentation\Requests;

use App\Core\Money\Money;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

final class ExecuteToolRequest extends FormRequest
{
    public function authorize(): bool{return true;}
    public function rules(): array
    {
        $m=['required','us_money','money_min:0'];
        return ['name'=>['required','string','max:150'],'employee_id'=>['required','string','max:50'],'payer'=>['required','string','max:150'],'year'=>['required','integer','min:2000','max:2100'],'gross'=>['required','us_money','money_min:0.01'],'federal_income_tax'=>$m,'social_security'=>$m,'medicare'=>$m,'state_local_tax'=>$m,'other_deductions'=>$m];
    }
    public function after(): array
    {
        return [function(Validator $validator): void{
            if($validator->errors()->isNotEmpty())return;
            $d=Money::fromDecimal((string)$this->input('federal_income_tax'))->add(Money::fromDecimal((string)$this->input('social_security')))->add(Money::fromDecimal((string)$this->input('medicare')))->add(Money::fromDecimal((string)$this->input('state_local_tax')))->add(Money::fromDecimal((string)$this->input('other_deductions')));
            if($d->minorAmount()>Money::fromDecimal((string)$this->input('gross'))->minorAmount()){$validator->errors()->add('other_deductions','Total withholding and deductions cannot exceed gross earnings.');}
        }];
    }
}
