<?php

declare(strict_types=1);

namespace App\Tools\BreakEvenCalculator\Presentation\Requests;

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
        return [
            'fixed_costs' => ['required', 'brazilian_money', 'money_min:0'],
            'sale_price' => ['required', 'brazilian_money', 'money_min:0.01'],
            'variable_cost' => ['required', 'brazilian_money', 'money_min:0'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }
            $variable = Money::fromDecimal((string) $this->input('variable_cost'))->minorAmount();
            $price = Money::fromDecimal((string) $this->input('sale_price'))->minorAmount();
            if ($variable >= $price) {
                $validator->errors()->add('variable_cost', 'O custo variável deve ser menor que o preço de venda.');
            }
        }];
    }
}
