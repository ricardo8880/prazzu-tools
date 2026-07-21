<?php

declare(strict_types=1);

namespace App\Tools\VacationCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ExecuteToolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        $money = ['nullable', 'regex:/^\s*(?:R\$\s*)?\d{1,12}(?:\.\d{3})*(?:,\d{1,2})?\s*$/'];

        return [
            'monthly_salary' => ['required', 'regex:/^\s*(?:R\$\s*)?\d{1,12}(?:\.\d{3})*(?:,\d{1,2})?\s*$/'],
            'acquisition_start_date' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'vacation_start_date' => ['required', 'date_format:Y-m-d', 'after:acquisition_start_date'],
            'unjustified_absences' => ['nullable', 'integer', 'min:0', 'max:365'],
            'convert_one_third_to_cash' => ['nullable', 'boolean'],
            'commission_average' => $money,
            'overtime_average' => $money,
            'recurring_additions' => $money,
            'other_deductions' => $money,
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'monthly_salary.regex' => 'Informe o salário no formato 3.000,00.',
            'acquisition_start_date.before_or_equal' => 'O período aquisitivo não pode começar no futuro.',
            'vacation_start_date.after' => 'O início das férias deve ser posterior ao início do período aquisitivo.',
            '*.regex' => 'Informe os valores monetários no formato 1.000,00.',
        ];
    }
}
