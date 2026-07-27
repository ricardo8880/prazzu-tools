<?php

declare(strict_types=1);
namespace App\Tools\OvertimeCalculator\Presentation\Requests;
use Illuminate\Foundation\Http\FormRequest;
final class ExecuteToolRequest extends FormRequest
{
 public function authorize(): bool { return true; }
 public function rules(): array { return [
  'competence'=>['required','regex:/^2026-(?:0[1-9]|1[0-2])$/'],'base_salary'=>['required','brazilian_money','money_min:0.01'],'monthly_hours'=>['required','integer','min:1','max:744'],
  'overtime_50_hours'=>['nullable','regex:/^\d+(?:[\.,]\d{1,3})?$/'],'overtime_100_hours'=>['nullable','regex:/^\d+(?:[\.,]\d{1,3})?$/'],'custom_overtime_hours'=>['nullable','regex:/^\d+(?:[\.,]\d{1,3})?$/'],'custom_premium'=>['nullable','numeric','min:50','max:500'],
  'night_clock_hours'=>['nullable','regex:/^\d+(?:[\.,]\d{1,3})?$/'],'night_overtime_hours'=>['nullable','regex:/^\d+(?:[\.,]\d{1,3})?$/'],'night_overtime_premium'=>['nullable','numeric','min:50','max:500'],
  'working_days'=>['nullable','integer','min:0','max:31'],'rest_days'=>['nullable','integer','min:0','max:15'],'include_dsr'=>['nullable','boolean'],'include_reflexes'=>['nullable','boolean'],'confirm_assumptions'=>['accepted'],
 ]; }
 public function messages(): array { return ['competence.regex'=>'A versão normativa deste lote cobre competências de 2026.','confirm_assumptions.accepted'=>'Confirme as premissas antes de calcular.']; }
}
