<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class SaveCalculationRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:160'],
            'reference_month' => ['required', 'date_format:Y-m'],
            'annex' => ['required', 'in:I,II,III,IV,V'],
            'rbt12' => ['required', 'string'],
            'monthly_revenue' => ['required', 'string'],
        ];
    }
}
