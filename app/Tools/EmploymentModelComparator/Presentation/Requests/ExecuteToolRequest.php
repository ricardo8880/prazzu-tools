<?php

declare(strict_types=1);

namespace App\Tools\EmploymentModelComparator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ExecuteToolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $m = ['required', 'brazilian_money', 'money_min:0'];
        $p = ['required', 'numeric', 'min:0', 'max:100'];

        return ['clt_gross' => ['required', 'brazilian_money', 'money_min:0.01'], 'clt_benefits' => $m, 'clt_employee_deductions' => $p, 'clt_company_burden' => $p, 'pj_invoice' => ['required', 'brazilian_money', 'money_min:0.01'], 'pj_taxes' => $p, 'pj_expenses' => $m, 'autonomous_gross' => ['required', 'brazilian_money', 'money_min:0.01'], 'autonomous_deductions' => $p, 'autonomous_company_burden' => $p];
    }
}
