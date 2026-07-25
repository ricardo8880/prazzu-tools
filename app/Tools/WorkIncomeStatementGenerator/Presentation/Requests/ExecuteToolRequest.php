<?php

declare(strict_types=1);

namespace App\Tools\WorkIncomeStatementGenerator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ExecuteToolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:150'], 'document' => ['required', 'string', 'max:30'], 'employer' => ['required', 'string', 'max:150'], 'occupation' => ['required', 'string', 'max:100'], 'start_date' => ['required', 'date'], 'monthly_income' => ['required', 'brazilian_money', 'money_min:0.01'], 'city' => ['required', 'string', 'max:100'], 'issue_date' => ['required', 'date']];
    }
}
