<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreProfitDistributionCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ExecuteToolRequest extends FormRequest
{
    private const MONEY = '/^-?\d+(?:[.,]\d{1,2})?$/';

    private const NON_NEGATIVE_MONEY = '/^\d+(?:[.,]\d{1,2})?$/';

    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'competence' => ['required', 'regex:/^2026-(?:0[1-9]|1[0-2])$/'],
            'company_regime' => ['required', Rule::in(['simples_outside_annex_iv', 'simples_annex_iv', 'presumed_profit', 'actual_profit'])],
            'partner_label' => ['nullable', 'string', 'max:80'],
            'gross_pro_labore' => ['required', 'regex:'.self::NON_NEGATIVE_MONEY],
            'dependents' => ['nullable', 'integer', 'min:0', 'max:99'],
            'other_official_social_security' => ['nullable', 'regex:'.self::NON_NEGATIVE_MONEY],
            'ownership_percentage' => ['required', 'decimal:0,6', 'min:100', 'max:100'],
            'accounting_profit' => ['required', 'regex:'.self::NON_NEGATIVE_MONEY],
            'accumulated_losses' => ['nullable', 'regex:'.self::NON_NEGATIVE_MONEY],
            'reserves_and_unavailable_amounts' => ['nullable', 'regex:'.self::NON_NEGATIVE_MONEY],
            'adjustments' => ['nullable', 'regex:'.self::MONEY],
            'prior_distributions' => ['nullable', 'regex:'.self::NON_NEGATIVE_MONEY],
            'intended_distribution' => ['required', 'regex:'.self::NON_NEGATIVE_MONEY],
            'confirm_assumptions' => ['accepted'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'competence.regex' => 'Neste lote, informe uma competência válida de 2026.',
            'ownership_percentage.min' => 'O cálculo Essencial deste lote trabalha com um único sócio e participação de 100%.',
            'ownership_percentage.max' => 'O cálculo Essencial deste lote trabalha com um único sócio e participação de 100%.',
            'confirm_assumptions.accepted' => 'Confirme as premissas e limitações antes de calcular.',
            '*.regex' => 'Informe um valor monetário válido com até duas casas decimais.',
        ];
    }
}
