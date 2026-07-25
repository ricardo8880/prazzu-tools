<?php

declare(strict_types=1);

namespace App\Tools\LaborChargesCalculator\Presentation\Requests;

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
            'salary' => ['required', 'brazilian_money', 'money_min:0.01'], 'benefits' => ['required', 'brazilian_money', 'money_min:0'],
            'regime' => ['required', 'in:general,simples_annex_iv,simples_other'], 'rat' => ['required', 'numeric', 'min:0', 'max:15'],
            'third_parties' => ['required', 'numeric', 'min:0', 'max:15'],
        ];
    }
}
