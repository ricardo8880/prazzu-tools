<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreProfitDistributionCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class SimulateScenariosRequest extends FormRequest
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
        $regimes = ['simples_outside_annex_iv', 'simples_annex_iv', 'presumed_profit', 'actual_profit'];

        return [
            'scenarios' => ['required', 'array', 'min:2', 'max:4'],
            'scenarios.*.name' => ['required', 'string', 'max:60'],
            'scenarios.*.periods' => ['required', 'array', 'min:1', 'max:12'],
            'scenarios.*.periods.*.competence' => ['required', 'regex:/^2026-(?:0[1-9]|1[0-2])$/'],
            'scenarios.*.periods.*.company_regime' => ['required', Rule::in($regimes)],
            'scenarios.*.periods.*.accounting_profit' => ['required', 'regex:'.self::NON_NEGATIVE_MONEY],
            'scenarios.*.periods.*.accumulated_losses' => ['nullable', 'regex:'.self::NON_NEGATIVE_MONEY],
            'scenarios.*.periods.*.reserves_and_unavailable_amounts' => ['nullable', 'regex:'.self::NON_NEGATIVE_MONEY],
            'scenarios.*.periods.*.adjustments' => ['nullable', 'regex:'.self::MONEY],
            'scenarios.*.periods.*.prior_distributions' => ['nullable', 'regex:'.self::NON_NEGATIVE_MONEY],
            'scenarios.*.periods.*.intended_distribution' => ['required', 'regex:'.self::NON_NEGATIVE_MONEY],
            'scenarios.*.periods.*.partners' => ['required', 'array', 'min:1', 'max:10'],
            'scenarios.*.periods.*.partners.*.label' => ['nullable', 'string', 'max:80'],
            'scenarios.*.periods.*.partners.*.ownership_percentage' => ['required', 'decimal:0,6', 'gt:0', 'lte:100'],
            'scenarios.*.periods.*.partners.*.gross_pro_labore' => ['required', 'regex:'.self::NON_NEGATIVE_MONEY],
            'scenarios.*.periods.*.partners.*.dependents' => ['nullable', 'integer', 'min:0', 'max:99'],
            'scenarios.*.periods.*.partners.*.other_official_social_security' => ['nullable', 'regex:'.self::NON_NEGATIVE_MONEY],
            'confirm_simulation_assumptions' => ['accepted'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'scenarios.min' => 'Informe pelo menos dois cenários para comparação.',
            'scenarios.max' => 'Compare no máximo quatro cenários por vez.',
            'scenarios.*.periods.max' => 'Cada cenário aceita no máximo doze competências.',
            'scenarios.*.periods.*.partners.max' => 'Cada competência aceita no máximo dez sócios.',
            'scenarios.*.periods.*.competence.regex' => 'Neste lote, use competências válidas de 2026.',
            'confirm_simulation_assumptions.accepted' => 'Confirme as premissas antes de simular.',
            '*.regex' => 'Informe valores monetários válidos com até duas casas decimais.',
        ];
    }
}
