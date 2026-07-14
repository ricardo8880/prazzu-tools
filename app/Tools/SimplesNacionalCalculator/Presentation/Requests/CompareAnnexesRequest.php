<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CompareAnnexesRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'annexes' => ['required', 'array', 'min:2'],
            'annexes.*' => ['required', 'distinct', 'in:I,II,III,IV,V'],
            'rbt12' => ['required', 'string'],
            'monthly_revenue' => ['required', 'string'],
        ];
    }
}
