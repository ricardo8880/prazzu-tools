<?php

declare(strict_types=1);

namespace App\Tools\ProfitDistributionCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

final class ExecuteToolRequest extends FormRequest
{
    private const MONEY = '/^-?\d+(?:[.,]\d{1,2})?$/';

    private const NON_NEGATIVE = '/^\d+(?:[.,]\d{1,2})?$/';

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
            'accounting_profit' => ['required', 'regex:'.self::NON_NEGATIVE],
            'accumulated_losses' => ['nullable', 'regex:'.self::NON_NEGATIVE],
            'reserves_and_unavailable_amounts' => ['nullable', 'regex:'.self::NON_NEGATIVE],
            'adjustments' => ['nullable', 'regex:'.self::MONEY],
            'prior_distributions' => ['nullable', 'regex:'.self::NON_NEGATIVE],
            'intended_distribution' => ['required', 'regex:'.self::NON_NEGATIVE],
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
                $validator->errors()->add('partners', 'A soma das participações dos sócios deve ser exatamente 100%.');
            }
        });
    }
}
