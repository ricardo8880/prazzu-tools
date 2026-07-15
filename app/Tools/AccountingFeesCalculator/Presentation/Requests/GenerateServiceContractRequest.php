<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Presentation\Requests;

use App\Tools\AccountingFeesCalculator\Domain\Enums\AccountingService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class GenerateServiceContractRequest extends FormRequest
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
            'client_representative' => ['required', 'string', 'max:120'],
            'accounting_firm' => ['required', 'string', 'max:150'],
            'accounting_firm_document' => ['nullable', 'string', 'max:30'],
            'accounting_representative' => ['required', 'string', 'max:120'],
            'monthly_fee' => ['required', 'regex:/^\s*(?:R\$\s*)?\d{1,12}(?:\.\d{3})*(?:,\d{1,2})?\s*$/'],
            'due_day' => ['required', 'integer', 'between:1,28'],
            'start_date' => ['required', 'date_format:Y-m-d'],
            'duration_months' => ['required', 'integer', Rule::in([6, 12, 24, 36])],
            'adjustment_index' => ['required', Rule::in(['IPCA', 'INPC', 'IGP-M', 'Percentual acordado'])],
            'late_fee_percent' => ['required', 'integer', 'between:0,10'],
            'termination_notice_days' => ['required', 'integer', Rule::in([15, 30, 45, 60, 90])],
            'services' => ['required', 'array', 'min:1'],
            'services.*' => ['required', Rule::enum(AccountingService::class)],
            'includes_lgpd' => ['nullable', 'boolean'],
            'includes_confidentiality' => ['nullable', 'boolean'],
            'additional_terms' => ['nullable', 'string', 'max:3000'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'monthly_fee.regex' => 'Informe o honorário mensal no formato 1.500,00.',
            'services.required' => 'Selecione ao menos um serviço para o contrato.',
            'services.min' => 'Selecione ao menos um serviço para o contrato.',
            'start_date.date_format' => 'Informe uma data de início válida.',
        ];
    }
}
