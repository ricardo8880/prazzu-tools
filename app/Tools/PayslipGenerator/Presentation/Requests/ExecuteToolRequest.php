<?php

declare(strict_types=1);

namespace App\Tools\PayslipGenerator\Presentation\Requests;

use App\Core\Money\Money;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

final class ExecuteToolRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $m = ['required', 'us_money', 'money_min:0'];
        return [
            'employee_name' => ['required','string','max:150'],
            'employee_id' => ['required','string','max:50'],
            'employer' => ['required','string','max:150'],
            'pay_period' => ['required','string','max:80'],
            'regular_pay' => ['required','us_money','money_min:0.01'],
            'overtime_pay' => $m,
            'other_earnings' => $m,
            'federal_income_tax' => $m,
            'social_security' => $m,
            'medicare' => $m,
            'state_local_tax' => $m,
            'other_deductions' => $m,
        ];
    }
    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) { return; }
            $earnings = Money::fromDecimal((string) $this->input('regular_pay'))
                ->add(Money::fromDecimal((string) $this->input('overtime_pay')))
                ->add(Money::fromDecimal((string) $this->input('other_earnings')));
            $deductions = Money::fromDecimal((string) $this->input('federal_income_tax'))
                ->add(Money::fromDecimal((string) $this->input('social_security')))
                ->add(Money::fromDecimal((string) $this->input('medicare')))
                ->add(Money::fromDecimal((string) $this->input('state_local_tax')))
                ->add(Money::fromDecimal((string) $this->input('other_deductions')));
            if ($deductions->minorAmount() > $earnings->minorAmount()) {
                $validator->errors()->add('other_deductions', 'Total deductions cannot exceed gross earnings.');
            }
        }];
    }
}
