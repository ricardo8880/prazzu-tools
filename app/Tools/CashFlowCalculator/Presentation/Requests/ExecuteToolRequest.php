<?php

declare(strict_types=1);

namespace App\Tools\CashFlowCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ExecuteToolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $nonNegative = ['required', 'brazilian_money', 'money_min:0'];

        return [
            'opening_balance' => ['required', 'brazilian_money'],
            'sales_receipts' => $nonNegative,
            'other_inflows' => $nonNegative,
            'operating_payments' => $nonNegative,
            'tax_payments' => $nonNegative,
            'investments' => $nonNegative,
            'financing_payments' => $nonNegative,
            'other_outflows' => $nonNegative,
        ];
    }
}
