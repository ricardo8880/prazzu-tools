<?php

declare(strict_types=1);
namespace App\Tools\LaborTerminationCalculator\Presentation\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
final class CalculateLaborTerminationRequest extends FormRequest
{
    public function authorize(): bool{return true;}
    public function rules(): array{return [
        'termination_date'=>['required','date'],
        'separation_type'=>['required',Rule::in(['resignation','layoff','termination','contract_end','other'])],
        'hourly_rate'=>['required','us_money','money_min:0'],
        'regular_hours_due'=>['required','numeric','min:0','max:744'],
        'unused_pto_hours'=>['nullable','numeric','min:0','max:5000'],
        'pto_payout_percent'=>['required','numeric','min:0','max:100'],
        'weekly_base_pay'=>['nullable','us_money','money_min:0'],
        'severance_weeks'=>['nullable','numeric','min:0','max:520'],
        'bonus_commission_due'=>['nullable','us_money','money_min:0'],
        'other_pay'=>['nullable','us_money','money_min:0'],
        'federal_withholding'=>['nullable','us_money','money_min:0'],
        'state_local_withholding'=>['nullable','us_money','money_min:0'],
        'other_deductions'=>['nullable','us_money','money_min:0'],
    ];}
}
