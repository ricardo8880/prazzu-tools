<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Presentation\Requests;

use App\Tools\AccountingFeesCalculator\Domain\Enums\AccountingService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class GenerateServiceContractRequest extends FormRequest
{
    public function authorize(): bool { return true; }

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
            'monthly_fee' => ['required', 'us_money', 'money_min:0.01'],
            'due_day' => ['required', 'integer', 'between:1,28'],
            'start_date' => ['required', 'date_format:Y-m-d'],
            'duration_months' => ['required', 'integer', Rule::in([6, 12, 24, 36])],
            'adjustment_index' => ['required', Rule::in(['CPI-U', 'Fixed percentage', 'Mutually agreed adjustment'])],
            'late_fee_percent' => ['required', 'integer', 'between:0,10'],
            'termination_notice_days' => ['required', 'integer', Rule::in([15, 30, 45, 60, 90])],
            'services' => ['required', 'array', 'min:1'],
            'services.*' => ['required', Rule::enum(AccountingService::class)],
            'includes_data_privacy' => ['nullable', 'boolean'],
            'includes_confidentiality' => ['nullable', 'boolean'],
            'additional_terms' => ['nullable', 'string', 'max:3000'],
        ];
    }

    public function messages(): array
    {
        return [
            'services.required' => 'Select at least one service for the agreement.',
            'services.min' => 'Select at least one service for the agreement.',
            'start_date.date_format' => 'Enter a valid start date.',
        ];
    }
}
