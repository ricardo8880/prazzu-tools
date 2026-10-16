<?php

declare(strict_types=1);
namespace App\Tools\VacationCalculator\Presentation\Requests;
use Illuminate\Foundation\Http\FormRequest;
final class ExecuteToolRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $hours=['required','regex:/^\d+(?:[\.,]\d{1,3})?$/'];
        return [
            'hourly_rate'=>['required','us_money','money_min:0.01'],
            'accrued_pto_hours'=>$hours,
            'planned_pto_hours'=>$hours,
            'accrual_hours_per_pay_period'=>['nullable','regex:/^\d+(?:[\.,]\d{1,3})?$/'],
            'pay_periods_before_leave'=>['nullable','integer','min:0','max:100'],
            'payout_percent'=>['required','numeric','min:0','max:100'],
        ];
    }
}
