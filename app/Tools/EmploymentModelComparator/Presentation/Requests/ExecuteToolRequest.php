<?php

declare(strict_types=1);

namespace App\Tools\EmploymentModelComparator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ExecuteToolRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $m=['required','us_money','money_min:0']; $p=['required','numeric','min:0','max:100'];
        return [
            'w2_gross'=>['required','us_money','money_min:0.01'],'w2_benefits'=>$m,'w2_employee_deductions'=>$p,'w2_employer_burden'=>$p,
            'contractor_revenue'=>['required','us_money','money_min:0.01'],'contractor_tax_rate'=>$p,'contractor_expenses'=>$m,
            'self_employed_revenue'=>['required','us_money','money_min:0.01'],'self_employed_tax_rate'=>$p,'self_employed_expenses'=>$m,
        ];
    }
}
