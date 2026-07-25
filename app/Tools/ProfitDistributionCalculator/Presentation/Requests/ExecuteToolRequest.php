<?php

declare(strict_types=1);

namespace App\Tools\ProfitDistributionCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ExecuteToolRequest extends FormRequest
{
    private const MONEY = '/^-?\d+(?:[.,]\d{1,2})?$/';
    private const NON_NEGATIVE = '/^\d+(?:[.,]\d{1,2})?$/';

    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'partner_label' => ['nullable', 'string', 'max:80'],
            'ownership_percentage' => ['required', 'decimal:0,6', 'min:100', 'max:100'],
            'accounting_profit' => ['required', 'regex:'.self::NON_NEGATIVE],
            'accumulated_losses' => ['nullable', 'regex:'.self::NON_NEGATIVE],
            'reserves_and_unavailable_amounts' => ['nullable', 'regex:'.self::NON_NEGATIVE],
            'adjustments' => ['nullable', 'regex:'.self::MONEY],
            'prior_distributions' => ['nullable', 'regex:'.self::NON_NEGATIVE],
            'intended_distribution' => ['required', 'regex:'.self::NON_NEGATIVE],
            'confirm_assumptions' => ['accepted'],
        ];
    }
}
