<?php

declare(strict_types=1);

namespace App\Tools\TaxInstallmentCalculator\Presentation\Requests;

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
        return [
            'debt_amount' => ['required', 'us_money', 'money_min:0.01'],
            'installments' => ['required', 'integer', 'min:1', 'max:72'],
            'monthly_charge' => ['required', 'percentage_value', 'percentage_min:0', 'percentage_max:100'],
            'entry_amount' => ['nullable', 'us_money', 'money_min:0'],
            'scenarios' => ['nullable', 'array', 'max:5'],
            'scenarios.*.name' => ['nullable', 'string', 'max:80'],
            'scenarios.*.entry_amount' => ['nullable', 'us_money', 'money_min:0'],
            'scenarios.*.installments' => ['nullable', 'integer', 'min:1', 'max:72'],
            'scenarios.*.monthly_charge' => ['nullable', 'percentage_value', 'percentage_min:0', 'percentage_max:100'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }
            try {
                $debt = Money::fromDecimal((string) $this->input('debt_amount'));
                $entry = Money::fromDecimal((string) ($this->input('entry_amount', '0') ?: '0'));
                if ($entry->minorAmount() >= $debt->minorAmount()) {
                    $validator->errors()->add('entry_amount', 'The upfront payment must be less than the tax balance.');
                }
                foreach ((array) $this->input('scenarios', []) as $index => $scenario) {
                    $hasValues = trim((string) ($scenario['name'] ?? '')) !== ''
                        || trim((string) ($scenario['entry_amount'] ?? '')) !== ''
                        || trim((string) ($scenario['installments'] ?? '')) !== ''
                        || trim((string) ($scenario['monthly_charge'] ?? '')) !== '';
                    if (! $hasValues) {
                        continue;
                    }
                    $scenarioEntry = Money::fromDecimal((string) (($scenario['entry_amount'] ?? '0') ?: '0'));
                    if ($scenarioEntry->minorAmount() >= $debt->minorAmount()) {
                        $validator->errors()->add("scenarios.$index.entry_amount", 'The scenario upfront payment must be less than the tax balance.');
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
            'debt_amount.required' => 'Enter the tax balance.',
            'installments.required' => 'Enter the number of monthly payments.',
            'monthly_charge.required' => 'Enter an estimated monthly combined interest/penalty rate, even if zero.',
        ];
    }
}
