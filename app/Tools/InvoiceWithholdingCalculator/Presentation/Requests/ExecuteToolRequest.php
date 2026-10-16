<?php

declare(strict_types=1);
namespace App\Tools\InvoiceWithholdingCalculator\Presentation\Requests;
use Illuminate\Foundation\Http\FormRequest;
final class ExecuteToolRequest extends FormRequest
{
    public function authorize():bool{return true;}
    public function rules():array{return [
        'competence'=>['required','date_format:Y-m','after_or_equal:2026-01','before_or_equal:2026-12'],
        'payment_reference'=>['nullable','string','max:60'],'payment_description'=>['required','string','max:160'],'gross_value'=>['required','us_money','money_min:0.01'],
        'apply_backup_withholding'=>['nullable','boolean'],'backup_withholding_rate'=>['required','percentage_value','percentage_min:0','percentage_max:100'],
        'state_withholding'=>['nullable','us_money','money_min:0'],'other_withholding'=>['nullable','us_money','money_min:0'],
        'additional_payments'=>['nullable','array','max:20'],'additional_payments.*.description'=>['nullable','string','max:120'],'additional_payments.*.value'=>['nullable','us_money','money_min:0'],
        'confirm_scope'=>['accepted'],
    ];}
    public function messages():array{return ['confirm_scope.accepted'=>'Confirm that you reviewed whether backup withholding applies to this payment.'];}
}
