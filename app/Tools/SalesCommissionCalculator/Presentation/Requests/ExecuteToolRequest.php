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
            'sales' => ['required', 'us_money', 'money_min:0'],
            'reversals' => ['nullable', 'us_money', 'money_min:0'],
            'rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'goal' => ['nullable', 'us_money', 'money_min:0'],
            'goal_bonus_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }
}
