<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Presentation\Requests;

use App\Tools\AccountingFeesCalculator\Domain\Enums\BusinessSegment;
use App\Tools\AccountingFeesCalculator\Domain\Enums\OperationalComplexity;
use App\Tools\AccountingFeesCalculator\Domain\Enums\TaxRegime;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CalculateAccountingFeesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'monthly_revenue' => ['required', 'regex:/^\s*(?:R\$\s*)?\d{1,12}(?:\.\d{3})*(?:,\d{1,2})?\s*$/'],
            'employees' => ['required', 'integer', 'min:0', 'max:10000'],
            'partners' => ['required', 'integer', 'min:1', 'max:1000'],
            'monthly_invoices' => ['required', 'integer', 'min:0', 'max:1000000'],
            'monthly_bank_transactions' => ['required', 'integer', 'min:0', 'max:1000000'],
            'tax_regime' => ['required', Rule::enum(TaxRegime::class)],
            'business_segment' => ['required', Rule::enum(BusinessSegment::class)],
            'complexity' => ['required', Rule::enum(OperationalComplexity::class)],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'monthly_revenue.regex' => 'Informe o faturamento no formato 100.000,00.',
            'partners.min' => 'Informe pelo menos um sócio ou titular.',
        ];
    }
}
