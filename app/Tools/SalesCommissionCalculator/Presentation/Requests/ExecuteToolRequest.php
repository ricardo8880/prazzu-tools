<?php

declare(strict_types=1);

namespace App\Tools\SalesCommissionCalculator\Presentation\Requests;

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
            'sales' => ['required', 'brazilian_money', 'money_min:0'],
            'reversals' => ['nullable', 'brazilian_money', 'money_min:0'],
            'rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'goal' => ['required', 'brazilian_money', 'money_min:0'],
            'goal_bonus_rate' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }
}
