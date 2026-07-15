<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Presentation\Requests;

use App\Tools\AccountingFeesCalculator\Domain\Enums\AccountingService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class GenerateCommercialProposalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'client_company' => ['required', 'string', 'max:150'],
            'client_document' => ['nullable', 'string', 'max:30'],
            'contact_name' => ['required', 'string', 'max:120'],
            'accounting_firm' => ['required', 'string', 'max:150'],
            'monthly_fee' => ['required', 'regex:/^\s*(?:R\$\s*)?\d{1,12}(?:\.\d{3})*(?:,\d{1,2})?\s*$/'],
            'setup_fee' => ['nullable', 'regex:/^\s*(?:R\$\s*)?\d{1,12}(?:\.\d{3})*(?:,\d{1,2})?\s*$/'],
            'due_day' => ['required', 'integer', 'between:1,28'],
            'validity_days' => ['required', 'integer', Rule::in([7, 15, 30, 45, 60])],
            'services' => ['required', 'array', 'min:1'],
            'services.*' => ['required', Rule::enum(AccountingService::class)],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'monthly_fee.regex' => 'Informe o honorário mensal no formato 1.500,00.',
            'setup_fee.regex' => 'Informe a implantação no formato 500,00.',
            'services.required' => 'Selecione ao menos um serviço para a proposta.',
            'services.min' => 'Selecione ao menos um serviço para a proposta.',
        ];
    }
}
