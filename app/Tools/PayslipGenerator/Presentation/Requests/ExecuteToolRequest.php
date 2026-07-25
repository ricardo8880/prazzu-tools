<?php

declare(strict_types=1);

namespace App\Tools\PayslipGenerator\Presentation\Requests;

use App\Core\Money\Money;
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
        $m = ['required', 'brazilian_money', 'money_min:0'];

        return ['name' => ['required', 'string', 'max:150'], 'document' => ['required', 'string', 'max:30'], 'employer' => ['required', 'string', 'max:150'], 'competence' => ['required', 'date_format:Y-m'], 'salary' => ['required', 'brazilian_money', 'money_min:0.01'], 'other_earnings' => $m, 'inss' => $m, 'irrf' => $m, 'other_deductions' => $m];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $earnings = Money::fromDecimal((string) $this->input('salary'))
                ->add(Money::fromDecimal((string) $this->input('other_earnings')));
            $deductions = Money::fromDecimal((string) $this->input('inss'))
                ->add(Money::fromDecimal((string) $this->input('irrf')))
                ->add(Money::fromDecimal((string) $this->input('other_deductions')));

            if ($deductions->minorAmount() > $earnings->minorAmount()) {
                $validator->errors()->add('other_deductions', 'A soma dos descontos não pode superar os proventos.');
            }
        }];
    }
}
