<?php

declare(strict_types=1);

namespace App\Tools\RetroactiveDasRegularizationCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ExecuteToolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['competence' => ['required', 'date_format:Y-m'], 'revenue' => ['required', 'brazilian_money', 'money_min:0.01'], 'effective_rate' => ['required', 'brazilian_percentage', 'percentage_min:0', 'percentage_max:100'], 'due_date' => ['required', 'date'], 'update_date' => ['required', 'date', 'after_or_equal:due_date'], 'accumulated_selic' => ['required', 'brazilian_percentage', 'percentage_min:0', 'percentage_max:500'], 'competencies' => ['nullable', 'array', 'max:12'], 'competencies.*.competence' => ['nullable', 'date_format:Y-m'], 'competencies.*.revenue' => ['nullable', 'brazilian_money', 'money_min:0.01'], 'competencies.*.effective_rate' => ['nullable', 'brazilian_percentage', 'percentage_min:0', 'percentage_max:100'], 'competencies.*.due_date' => ['nullable', 'date'], 'competencies.*.update_date' => ['nullable', 'date'], 'competencies.*.accumulated_selic' => ['nullable', 'brazilian_percentage', 'percentage_min:0', 'percentage_max:500'], 'regularization_months' => ['nullable', 'integer', 'min:1', 'max:24']];
    }
}
