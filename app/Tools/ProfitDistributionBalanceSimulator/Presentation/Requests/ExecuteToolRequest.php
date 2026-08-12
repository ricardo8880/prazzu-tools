<?php

declare(strict_types=1);

namespace App\Tools\ProfitDistributionBalanceSimulator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ExecuteToolRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'annual_revenue' => ['required','brazilian_money','money_min:0.01'],
            'accounting_profit' => ['required','brazilian_money','money_min:0'],
            'reference_margin' => ['required','brazilian_percentage','percentage_min:0','percentage_max:100'],
            'taxes_on_revenue' => ['required','brazilian_money','money_min:0'],
            'prior_distributions' => ['nullable','brazilian_money','money_min:0'],
            'monthly_pro_labore' => ['nullable','brazilian_money','money_min:0'],
            'monthly_growth_rate' => ['nullable','brazilian_percentage','percentage_min:0','percentage_max:100'],
            'planning_months' => ['nullable','integer','min:1','max:24'],
            'simulate_bookkeeping' => ['nullable','boolean'],
            'operating_expenses' => ['nullable','brazilian_money','money_min:0'],
            'other_expenses' => ['nullable','brazilian_money','money_min:0'],
        ];
    }
}
