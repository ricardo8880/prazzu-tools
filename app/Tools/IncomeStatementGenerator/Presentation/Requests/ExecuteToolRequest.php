<?php

declare(strict_types=1);

namespace App\Tools\IncomeStatementGenerator\Presentation\Requests;

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

        return ['name' => ['required', 'string', 'max:150'], 'document' => ['required', 'string', 'max:30'], 'payer' => ['required', 'string', 'max:150'], 'year' => ['required', 'integer', 'min:2000', 'max:2100'], 'gross' => ['required', 'brazilian_money', 'money_min:0.01'], 'inss' => $m, 'irrf' => $m, 'other_deductions' => $m];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $deductions = Money::fromDecimal((string) $this->input('inss'))
                ->add(Money::fromDecimal((string) $this->input('irrf')))
                ->add(Money::fromDecimal((string) $this->input('other_deductions')));

            if ($deductions->minorAmount() > Money::fromDecimal((string) $this->input('gross'))->minorAmount()) {
                $validator->errors()->add('other_deductions', 'A soma das deduções não pode superar os rendimentos brutos.');
            }
        }];
    }
}
