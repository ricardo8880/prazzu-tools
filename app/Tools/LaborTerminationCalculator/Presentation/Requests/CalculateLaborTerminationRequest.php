<?php

declare(strict_types=1);

namespace App\Tools\LaborTerminationCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CalculateLaborTerminationRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        $money = ['nullable', 'regex:/^\s*(?:R\$\s*)?\d{1,10}(?:\.\d{3})*(?:,\d{1,2})?\s*$/'];

        return [
            'monthly_salary' => ['required', $money[1]],
            'admission_date' => ['required', 'date', 'before_or_equal:termination_date'],
            'termination_date' => ['required', 'date', 'after_or_equal:admission_date'],
            'termination_type' => ['required', Rule::in(['dismissal_without_cause','resignation','dismissal_with_cause','mutual_agreement','indirect_termination','contract_end','early_contract_end'])],
            'contract_type' => ['required', Rule::in(['indefinite', 'fixed_term', 'experience', 'domestic'])],
            'notice_type' => ['required', Rule::in(['worked', 'indemnified', 'not_worked', 'not_applicable'])],
            'days_worked_in_month' => ['required', 'integer', 'min:0', 'max:31'],
            'overdue_vacation_periods' => ['nullable', 'integer', 'min:0', 'max:3'],
            'double_vacation_periods' => ['nullable', 'integer', 'min:0', 'max:3'],
            'fgts_balance' => $money,
            'domestic_indemnity_reserve_balance' => $money,
            'other_discounts' => $money,
            'dependents' => ['nullable', 'integer', 'min:0', 'max:99'],
            'commission_average' => $money,
            'overtime_average' => $money,
            'recurring_additions' => $money,
            'contract_end_date' => ['nullable', 'date', 'after:termination_date', 'required_if:termination_type,early_contract_end'],
            'early_termination_initiative' => ['nullable', Rule::in(['employer', 'employee']), 'required_if:termination_type,early_contract_end'],
            'article_480_discount' => $money,
            'extraordinary_indemnities' => $money,
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'monthly_salary.required' => 'Informe o salário mensal.', 'monthly_salary.regex' => 'Informe o salário no formato 1.234,56.',
            'admission_date.required' => 'Informe a data de admissão.', 'admission_date.before_or_equal' => 'A admissão deve ser anterior ou igual à data de desligamento.',
            'termination_date.required' => 'Informe a data de desligamento.', 'termination_date.after_or_equal' => 'O desligamento deve ser posterior ou igual à data de admissão.',
            'termination_type.required' => 'Selecione o motivo da rescisão.', 'termination_type.in' => 'Selecione um motivo de rescisão válido.',
            'contract_type.required' => 'Selecione o tipo de contrato.', 'contract_type.in' => 'Selecione um tipo de contrato válido.',
            'notice_type.required' => 'Selecione como será tratado o aviso-prévio.', 'notice_type.in' => 'Selecione uma opção de aviso-prévio válida.',
            'days_worked_in_month.required' => 'Informe os dias trabalhados no mês do desligamento.', 'days_worked_in_month.integer' => 'Os dias trabalhados devem ser um número inteiro.',
            'days_worked_in_month.min' => 'Os dias trabalhados não podem ser negativos.', 'days_worked_in_month.max' => 'Os dias trabalhados não podem ser maiores que 31.',
            'overdue_vacation_periods.max' => 'Informe no máximo 3 períodos de férias vencidas.', 'double_vacation_periods.max' => 'Informe no máximo 3 períodos de férias em dobro.',
            'contract_end_date.required_if' => 'Informe a data prevista para o fim do contrato.', 'contract_end_date.after' => 'O fim previsto do contrato deve ser posterior ao desligamento.',
            'early_termination_initiative.required_if' => 'Informe quem antecipou o término do contrato.',
            'fgts_balance.regex' => 'Informe o saldo do FGTS no formato 12.345,67.', 'domestic_indemnity_reserve_balance.regex' => 'Informe a reserva indenizatória doméstica no formato 12.345,67.', 'other_discounts.regex' => 'Informe os outros descontos no formato 1.234,56.',
            'commission_average.regex' => 'Informe a média de comissões no formato 1.234,56.', 'overtime_average.regex' => 'Informe a média de horas extras no formato 1.234,56.',
            'recurring_additions.regex' => 'Informe os adicionais recorrentes no formato 1.234,56.', 'article_480_discount.regex' => 'Informe o desconto do art. 480 no formato 1.234,56.',
            'extraordinary_indemnities.regex' => 'Informe as indenizações adicionais no formato 1.234,56.',
        ];
    }
}
