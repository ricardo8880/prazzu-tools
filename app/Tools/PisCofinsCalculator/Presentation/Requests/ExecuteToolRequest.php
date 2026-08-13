<?php

declare(strict_types=1);

namespace App\Tools\PisCofinsCalculator\Presentation\Requests;

use App\Core\Money\Money;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Throwable;

final class ExecuteToolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $money = ['required', 'brazilian_money', 'money_min:0'];

        return [
            'period' => ['required', 'date_format:Y-m', 'after_or_equal:2026-01', 'before_or_equal:2026-12'],
            'regime' => ['required', 'in:cumulative,non_cumulative'],
            'compare_regimes' => ['nullable', 'boolean'],
            'taxable_revenue' => $money,
            'credit_base' => $money,
            'pis_withheld' => $money,
            'cofins_withheld' => $money,
            'operations' => ['nullable', 'array', 'max:20'],
            'operations.*.description' => ['nullable', 'string', 'max:80'],
            'operations.*.revenue' => ['nullable', 'brazilian_money', 'money_min:0'],
            'operations.*.credit_base' => ['nullable', 'brazilian_money', 'money_min:0'],
            'confirm_scope' => ['accepted'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }
            try {
                $total = Money::fromDecimal((string) $this->input('taxable_revenue', '0'));
                foreach ((array) $this->input('operations', []) as $operation) {
                    $value = trim((string) ($operation['revenue'] ?? ''));
                    if ($value !== '') {
                        $total = $total->add(Money::fromDecimal($value));
                    }
                }
                if ($total->minorAmount() <= 0) {
                    $validator->errors()->add('taxable_revenue', 'Informe uma base tributável maior que zero.');
                }
            } catch (Throwable) {
            }
        });
    }

    public function messages(): array
    {
        return [
            'confirm_scope.accepted' => 'Confirme que a base tributável e os créditos informados foram revisados para o caso concreto.',
            'period.after_or_equal' => 'Informe uma competência de 2026.',
            'period.before_or_equal' => 'Informe uma competência de 2026.',
        ];
    }
}
