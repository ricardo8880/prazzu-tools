<?php

declare(strict_types=1);

namespace App\Tools\EmployerInssCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ExecuteToolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payroll' => ['required', 'brazilian_money', 'money_min:0'],
            'regime' => ['required', 'in:general,simples_annex_iv,simples_other'],
            'adjusted_rat' => ['required', 'numeric', 'min:0', 'max:15'],
            'third_parties' => ['required', 'numeric', 'min:0', 'max:15'],
        ];
    }
}
