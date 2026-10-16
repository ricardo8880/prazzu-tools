<?php

declare(strict_types=1);
namespace App\Tools\OvertimeCalculator\Presentation\Requests;
use Illuminate\Foundation\Http\FormRequest;
final class ExecuteToolRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $hours=['required','regex:/^\d+(?:[\.,]\d{1,3})?$/'];
        return [
            'competence'=>['required','regex:/^2026-(?:0[1-9]|1[0-2])$/'],
            'hourly_rate'=>['required','us_money','money_min:0.01'],
            'regular_hours'=>$hours,
            'overtime_hours'=>$hours,
            'overtime_multiplier'=>['required','numeric','min:1.5','max:5'],
            'nondiscretionary_bonus'=>['nullable','us_money','money_min:0'],
            'confirm_assumptions'=>['accepted'],
        ];
    }
    public function messages(): array { return ['confirm_assumptions.accepted'=>'Confirm the assumptions before calculating.']; }
}
