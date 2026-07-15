<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAccountingClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:150'],
            'document' => ['nullable', 'string', 'max:30'],
            'contact_name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email:rfc', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'monthly_fee' => ['required', 'string', 'max:30', 'regex:/^\s*(?:R\$\s*)?\d{1,3}(?:\.\d{3})*(?:,\d{1,2})?\s*$|^\s*(?:R\$\s*)?\d+(?:,\d{1,2})?\s*$/'],
            'proposal_status' => ['required', Rule::in(['not_created', 'draft', 'sent', 'accepted', 'rejected'])],
            'contract_status' => ['required', Rule::in(['not_created', 'draft', 'sent', 'signed', 'cancelled'])],
            'pipeline_status' => ['required', Rule::in(['prospect', 'negotiation', 'client', 'inactive'])],
            'notes' => ['nullable', 'string', 'max:3000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'company_name' => 'empresa',
            'contact_name' => 'responsável',
            'monthly_fee' => 'honorário mensal',
            'proposal_status' => 'situação da proposta',
            'contract_status' => 'situação do contrato',
            'pipeline_status' => 'etapa do CRM',
        ];
    }
}
