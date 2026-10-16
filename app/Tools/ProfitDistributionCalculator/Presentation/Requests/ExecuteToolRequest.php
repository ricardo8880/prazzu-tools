<?php

declare(strict_types=1);

namespace App\Tools\ProfitDistributionCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

final class ExecuteToolRequest extends FormRequest
{
        public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'partner_label' => ['nullable', 'string', 'max:80'],
            'ownership_percentage' => ['required', 'decimal:0,6', 'min:0.000001', 'max:100'],
            'partners' => ['nullable', 'array', 'max:19'],
            'partners.*.label' => ['nullable', 'string', 'max:80'],
            'partners.*.ownership_percentage' => ['nullable', 'decimal:0,6', 'min:0.000001', 'max:100'],
            'accounting_profit' => ['required', 'us_money', 'money_min:0'],
            'accumulated_losses' => ['nullable', 'us_money', 'money_min:0'],
            'reserves_and_unavailable_amounts' => ['nullable', 'us_money', 'money_min:0'],
            'adjustments' => ['nullable', 'us_money'],
            'prior_distributions' => ['nullable', 'us_money', 'money_min:0'],
            'intended_distribution' => ['required', 'us_money', 'money_min:0'],
            'confirm_assumptions' => ['accepted'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $percentages = [(string) $this->input('ownership_percentage')];
            foreach ((array) $this->input('partners', []) as $partner) {
                if (trim((string) ($partner['ownership_percentage'] ?? '')) !== '') {
                    $percentages[] = (string) $partner['ownership_percentage'];
                }
            }

            $scaled = 0;
            foreach ($percentages as $percentage) {
                $normalized = str_replace(',', '.', trim($percentage));
                [$whole, $fraction] = array_pad(explode('.', $normalized, 2), 2, '');
                $scaled += ((int) $whole * 1_000_000) + (int) str_pad(substr($fraction, 0, 6), 6, '0');
            }

            if ($scaled !== 100_000_000) {
                $validator->errors()->add('partners', 'Ownership percentages must total exactly 100%.');
            }
        });
    }
}
