<?php

declare(strict_types=1);

namespace App\Tools\WorkingCapitalCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ExecuteToolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'cash' => ['required', 'us_money', 'money_min:0'],
            'receivables' => ['required', 'us_money', 'money_min:0'],
            'inventory' => ['required', 'us_money', 'money_min:0'],
            'other_current_assets' => ['required', 'us_money', 'money_min:0'],
            'suppliers' => ['required', 'us_money', 'money_min:0'],
            'other_operating_liabilities' => ['required', 'us_money', 'money_min:0'],
            'loans' => ['required', 'us_money', 'money_min:0'],
            'other_current_liabilities' => ['required', 'us_money', 'money_min:0'],
        ];
    }
}
