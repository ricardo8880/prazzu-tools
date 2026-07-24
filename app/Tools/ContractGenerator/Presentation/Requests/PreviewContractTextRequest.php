<?php

declare(strict_types=1);

namespace App\Tools\ContractGenerator\Presentation\Requests;

use App\Tools\ContractGenerator\Domain\Enums\ContractType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class PreviewContractTextRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'contract_type' => ['required', Rule::enum(ContractType::class)],
            'contract_text' => ['required', 'string', 'min:20', 'max:60000'],
        ];
    }

    protected function getRedirectUrl(): string
    {
        return route('tools.gerador-de-contratos.index', ['tipo' => $this->input('contract_type')]);
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('contract_text') && is_string($this->input('contract_text'))) {
            $this->merge(['contract_text' => trim((string) $this->input('contract_text'))]);
        }
    }
}
