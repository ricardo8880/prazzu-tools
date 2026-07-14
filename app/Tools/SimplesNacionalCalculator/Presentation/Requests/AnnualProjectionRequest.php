<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class AnnualProjectionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'annex' => ['required', 'in:I,II,III,IV,V'],
            'monthly_revenue' => ['required', 'string'],
            'monthly_growth' => ['required', 'numeric', 'between:-50,100'],
        ];
    }
}
