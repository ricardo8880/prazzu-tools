<?php

declare(strict_types=1);

namespace App\Tools\FactorRSimulator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ExecuteToolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payroll_12' => ['required', 'brazilian_money', 'money_min:0'],
            'revenue_12' => ['required', 'brazilian_money', 'money_min:0'],
        ];
    }
}
