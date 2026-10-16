<?php

declare(strict_types=1);

namespace App\Tools\ProfitDistributionBalanceSimulator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ExecuteToolRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $money = ['required', 'us_money', 'money_min:0'];
        return [
            'retained_earnings' => $money,
            'current_year_net_income' => $money,
            'cash_available' => $money,
            'minimum_cash_reserve' => $money,
            'prior_distributions' => ['nullable', 'us_money', 'money_min:0'],
            'planned_distribution' => ['nullable', 'us_money', 'money_min:0'],
            'monthly_net_income' => ['nullable', 'us_money', 'money_min:0'],
            'monthly_growth_rate' => ['nullable', 'percentage_value', 'percentage_min:0', 'percentage_max:100'],
            'planning_months' => ['nullable', 'integer', 'min:1', 'max:24'],
        ];
    }
}
