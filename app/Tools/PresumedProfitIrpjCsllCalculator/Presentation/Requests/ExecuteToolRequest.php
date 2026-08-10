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
            'quarter' => ['required', 'integer', 'between:1,4'],
            'commerce_revenue' => $money,
            'fuel_revenue' => $money,
            'passenger_transport_revenue' => $money,
            'services_revenue' => $money,
            'other_taxable_additions' => $money,
            'prior_irpj_presumption_revenue' => $money,
            'prior_csll_presumption_revenue' => $money,
            'irpj_credits' => $money,
            'csll_credits' => $money,
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

                $quarter = (int) $this->input('quarter');
                $priorIrpj = Money::fromDecimal((string) $this->input('prior_irpj_presumption_revenue', '0'));
                $priorCsll = Money::fromDecimal((string) $this->input('prior_csll_presumption_revenue', '0'));

                if ($quarter === 1 && $priorIrpj->minorAmount() > 0) {
                    $validator->errors()->add('prior_irpj_presumption_revenue', 'No 1º trimestre não há receita anterior de 2026 para consumir o limite do IRPJ.');
                }

                if ($quarter <= 2 && $priorCsll->minorAmount() > 0) {
                    $validator->errors()->add('prior_csll_presumption_revenue', 'Para a CSLL, a elevação dos percentuais começa no 2º trimestre de 2026; informe receita anterior somente a partir do 3º trimestre.');
                }
            } catch (Throwable) {
                // As regras de formato monetário produzem as mensagens primárias.
            }
        });
    }

    public function messages(): array
    {
        return [
            'confirm_scope.accepted' => 'Confirme que os percentuais de presunção e as adições informadas são aplicáveis ao caso concreto.',
        ];
    }
}
