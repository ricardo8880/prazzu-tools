<?php

declare(strict_types=1);

namespace App\Tools\TaxInstallmentCalculator\Presentation\Requests;

use App\Core\Money\Money;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Throwable;

final class ExecuteToolRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'debt_amount' => ['required', 'brazilian_money', 'money_min:0.01'],
            'installments' => ['required', 'integer', 'min:1', 'max:240'],
            'monthly_charge' => ['required', 'brazilian_percentage', 'percentage_min:0', 'percentage_max:100'],
            'entry_amount' => ['nullable', 'brazilian_money', 'money_min:0'],
            'scenarios' => ['nullable', 'array', 'max:5'],
            'scenarios.*.name' => ['nullable', 'string', 'max:80'],
            'scenarios.*.entry_amount' => ['nullable', 'brazilian_money', 'money_min:0'],
            'scenarios.*.installments' => ['nullable', 'integer', 'min:1', 'max:240'],
            'scenarios.*.monthly_charge' => ['nullable', 'brazilian_percentage', 'percentage_min:0', 'percentage_max:100'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) return;
            try {
                $debt = Money::fromDecimal((string) $this->input('debt_amount'));
                $entry = Money::fromDecimal((string) ($this->input('entry_amount', '0') ?: '0'));
                if ($entry->minorAmount() >= $debt->minorAmount()) {
                    $validator->errors()->add('entry_amount', 'A entrada deve ser menor que a dívida.');
                }
                foreach ((array) $this->input('scenarios', []) as $index => $scenario) {
                    $hasValues = trim((string) ($scenario['name'] ?? '')) !== ''
                        || trim((string) ($scenario['entry_amount'] ?? '')) !== ''
                        || trim((string) ($scenario['installments'] ?? '')) !== ''
                        || trim((string) ($scenario['monthly_charge'] ?? '')) !== '';
                    if (! $hasValues) continue;
                    $scenarioEntry = Money::fromDecimal((string) (($scenario['entry_amount'] ?? '0') ?: '0'));
                    if ($scenarioEntry->minorAmount() >= $debt->minorAmount()) {
                        $validator->errors()->add("scenarios.$index.entry_amount", 'A entrada do cenário deve ser menor que a dívida.');
                    }
                }
            } catch (Throwable) {
                // Regras básicas já reportam formato inválido.
            }
        });
    }

    public function messages(): array
    {
        return [
            'debt_amount.required' => 'Informe o valor da dívida.',
            'installments.required' => 'Informe a quantidade de parcelas.',
            'monthly_charge.required' => 'Informe a taxa mensal de encargos, mesmo que seja zero.',
        ];
    }
}
