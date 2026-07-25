<?php

declare(strict_types=1);

namespace App\Tools\EmployeeCostCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreEmployeeProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'company_profile_id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:160'],
            'document' => ['nullable', 'string', 'max:30'],
            'department' => ['nullable', 'string', 'max:120'],
            'role' => ['nullable', 'string', 'max:120'],
            'salary' => ['required', 'brazilian_money', 'money_min:0.01'],
            'variable_pay' => ['required', 'brazilian_money', 'money_min:0'],
            'benefits' => ['required', 'brazilian_money', 'money_min:0'],
            'regime' => ['required', 'in:general,simples_annex_iv,simples_other'],
            'rat' => ['required', 'numeric', 'min:0', 'max:15'],
            'third_parties' => ['required', 'numeric', 'min:0', 'max:15'],
            'monthly_hours' => ['required', 'integer', 'min:1', 'max:744'],
        ];
    }
}
