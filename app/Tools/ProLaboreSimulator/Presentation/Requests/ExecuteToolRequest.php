<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreSimulator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ExecuteToolRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'competence' => ['required', 'regex:/^2026-(?:0[1-9]|1[0-2])$/'],
            'company_regime' => ['required', Rule::in(['simples_outside_annex_iv', 'simples_annex_iv', 'presumed_profit', 'actual_profit'])],
            'gross_pro_labore' => ['required', 'regex:/^\d+(?:[.,]\d{1,2})?$/'],
            'dependents' => ['nullable', 'integer', 'min:0', 'max:99'],
            'other_official_social_security' => ['nullable', 'regex:/^\d+(?:[.,]\d{1,2})?$/'],
            'confirm_assumptions' => ['accepted'],
        ];
    }
}
