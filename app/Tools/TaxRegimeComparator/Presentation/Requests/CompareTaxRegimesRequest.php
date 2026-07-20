<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Presentation\Requests;

use App\Tools\TaxRegimeComparator\Domain\Enums\BusinessActivity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CompareTaxRegimesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $money = ['required', 'string', 'regex:/^\s*(?:R\$\s*)?\d{1,12}(?:\.\d{3})*(?:,\d{1,2})?\s*$|^\s*(?:R\$\s*)?\d+(?:,\d{1,2})?\s*$/'];

        return [
            'reference_date' => ['required', 'date_format:Y-m-d'],
            'business_activity' => ['required', Rule::enum(BusinessActivity::class)],
            'monthly_revenue' => $money,
            'revenue_last_twelve_months' => $money,
            'payroll_last_twelve_months' => $money,
            'monthly_operating_costs' => $money,
            'monthly_deductible_expenses' => $money,
            'monthly_pis_cofins_credit_base' => ['nullable', ...array_slice($money, 1)],
            'indirect_tax_rate' => ['nullable', 'regex:/^\d{1,2}(?:[\.,]\d{1,6})?$/', 'not_regex:/^100(?:[\.,]0+)?$/'],
            'state' => ['nullable', 'string', 'size:2', 'regex:/^[A-Za-z]{2}$/'],
            'municipality' => ['nullable', 'string', 'max:120'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'reference_date.date_format' => 'Informe uma data de referência válida.',
            'monthly_revenue.regex' => 'Informe o faturamento mensal no formato 100.000,00.',
            'revenue_last_twelve_months.regex' => 'Informe a receita dos últimos 12 meses no formato 1.200.000,00.',
            'payroll_last_twelve_months.regex' => 'Informe a folha dos últimos 12 meses no formato 300.000,00.',
            'indirect_tax_rate.regex' => 'Informe a alíquota efetiva com até seis casas decimais.',
            'state.size' => 'Informe a UF com duas letras.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'state' => $this->filled('state') ? strtoupper(trim((string) $this->input('state'))) : null,
            'municipality' => $this->filled('municipality') ? trim((string) $this->input('municipality')) : null,
            'indirect_tax_rate' => $this->filled('indirect_tax_rate')
                ? str_replace(',', '.', trim((string) $this->input('indirect_tax_rate')))
                : null,
        ]);
    }
}
