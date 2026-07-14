<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CompareScenariosRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'scenarios' => ['required', 'array', 'min:2', 'max:4'],
            'scenarios.*.name' => ['required', 'string', 'max:80'],
            'scenarios.*.annex' => ['required', 'in:I,II,III,IV,V'],
            'scenarios.*.rbt12' => ['required', 'string'],
            'scenarios.*.monthly_revenue' => ['required', 'string'],
        ];
    }
}
