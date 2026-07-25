<?php

declare(strict_types=1);

namespace App\Tools\EmployeeCostCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreCompanyProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:160'],
            'legal_name' => ['nullable', 'string', 'max:200'],
            'document' => ['nullable', 'string', 'max:30'],
            'office_name' => ['nullable', 'string', 'max:160'],
            'accountant_name' => ['nullable', 'string', 'max:160'],
            'accountant_registration' => ['nullable', 'string', 'max:40'],
        ];
    }
}
