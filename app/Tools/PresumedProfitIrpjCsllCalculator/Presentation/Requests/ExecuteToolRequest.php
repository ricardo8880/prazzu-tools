<?php

declare(strict_types=1);

namespace App\Tools\PresumedProfitIrpjCsllCalculator\Presentation\Requests;

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
            'periodicity' => ['required', 'in:quarterly,monthly'],
            'quarter' => ['nullable', 'required_if:periodicity,quarterly', 'integer', 'between:1,4'],
            'month' => ['nullable', 'required_if:periodicity,monthly', 'integer', 'between:1,12'],
            'commerce_revenue' => $money,
            'fuel_revenue' => $money,
            'passenger_transport_revenue' => $money,
            'services_revenue' => $money,
            'other_taxable_additions' => $money,
            'prior_irpj_presumption_revenue' => $money,
            'prior_csll_presumption_revenue' => $money,
            'irpj_credits' => $money,
            'csll_credits' => $money,
            'scenarios' => ['nullable', 'array', 'max:3'],
            'scenarios.*.name' => ['nullable', 'string', 'max:80'],
            'scenarios.*.commerce_revenue' => ['nullable', 'brazilian_money', 'money_min:0'],
            'scenarios.*.fuel_revenue' => ['nullable', 'brazilian_money', 'money_min:0'],
            'scenarios.*.passenger_transport_revenue' => ['nullable', 'brazilian_money', 'money_min:0'],
            'scenarios.*.services_revenue' => ['nullable', 'brazilian_money', 'money_min:0'],
            'scenarios.*.other_taxable_additions' => ['nullable', 'brazilian_money', 'money_min:0'],
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
                $revenue = Money::zero();
                foreach (['commerce_revenue', 'fuel_revenue', 'passenger_transport_revenue', 'services_revenue'] as $field) {
                    $revenue = $revenue->add(Money::fromDecimal((string) $this->input($field, '0')));
                }
                if ($revenue->minorAmount() <= 0) {
                    $validator->errors()->add('commerce_revenue', 'Informe receita bruta em ao menos uma atividade.');
                }

                $periodicity = (string) $this->input('periodicity', 'quarterly');
                $period = $periodicity === 'monthly' ? (int) $this->input('month') : (int) $this->input('quarter');
                $priorIrpj = Money::fromDecimal((string) $this->input('prior_irpj_presumption_revenue', '0'));
                $priorCsll = Money::fromDecimal((string) $this->input('prior_csll_presumption_revenue', '0'));

                if ($period === 1 && $priorIrpj->minorAmount() > 0) {
                    $validator->errors()->add('prior_irpj_presumption_revenue', 'No primeiro período não há receita anterior de 2026 para consumir o limite do IRPJ.');
                }
                $csllPriorNotApplicable = $periodicity === 'monthly' ? $period <= 4 : $period <= 2;
                if ($csllPriorNotApplicable && $priorCsll->minorAmount() > 0) {
                    $validator->errors()->add('prior_csll_presumption_revenue', 'Informe receita anterior da CSLL somente quando já houver período anterior sujeito ao limite de 2026.');
                }
            } catch (Throwable) {
                // As regras de formato monetário produzem as mensagens primárias.
            }
        });
    }

    public function messages(): array
    {
        return ['confirm_scope.accepted' => 'Confirme que os percentuais de presunção e as adições informadas são aplicáveis ao caso concreto.'];
    }
}
