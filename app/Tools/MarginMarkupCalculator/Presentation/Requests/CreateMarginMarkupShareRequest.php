<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CreateMarginMarkupShareRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['access_code' => trim((string) $this->input('access_code')) ?: null]);
    }

    public function rules(): array
    {
        return [
            'validity_days' => ['required', 'integer', Rule::in([1, 7, 15, 30, 90])],
            'access_code' => ['nullable', 'string', 'min:4', 'max:40'],
        ];
    }
}
