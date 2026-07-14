<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class AnalyzeAlertsRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'annex' => ['required', Rule::in(['I', 'II', 'III', 'IV', 'V'])],
            'rbt12' => ['required', 'string'],
            'monthly_revenue' => ['required', 'string'],
            'payroll_12' => ['nullable', 'string'],
            'monthly_growth' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }
}
