<?php

declare(strict_types=1);

namespace App\Tools\SalaryAdjustmentCalculator\Presentation\Requests;

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
            'current_salary' => ['required', 'brazilian_money', 'money_min:0.01'],
            'adjustment_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'fixed_addition' => ['required', 'brazilian_money', 'money_min:0'],
            'retroactive_months' => ['required', 'integer', 'min:0', 'max:60'],
        ];
    }
}
