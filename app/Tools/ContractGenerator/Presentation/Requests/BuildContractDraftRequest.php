<?php

declare(strict_types=1);

namespace App\Tools\ContractGenerator\Presentation\Requests;

use App\Core\Money\Money;
use App\Tools\ContractGenerator\Domain\Enums\ContractTemplate;
use App\Tools\ContractGenerator\Domain\Enums\ContractType;
use App\Tools\ContractGenerator\Domain\Enums\PartyDocumentType;
use App\Tools\ContractGenerator\Domain\Enums\SmartClause;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

final class BuildContractDraftRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'contract_type' => ['required', Rule::enum(ContractType::class)],
            'template_key' => ['nullable', Rule::enum(ContractTemplate::class)],
            'smart_clauses' => ['sometimes', 'array', 'max:5'],
            'smart_clauses.*' => [Rule::enum(SmartClause::class)],
            'first_party_name' => ['required', 'string', 'min:2', 'max:180'],
            'first_party_document_type' => ['required', Rule::enum(PartyDocumentType::class)],
            'first_party_document' => ['required', 'string', 'max:40', $this->documentRule('first_party_document_type')],
            'first_party_address' => ['required', 'string', 'min:5', 'max:240'],
            'first_party_city' => ['required', 'string', 'min:2', 'max:120'],
            'first_party_state' => ['required', 'string', 'size:2', 'regex:/^[A-Za-z]{2}$/'],
            'second_party_name' => ['required', 'string', 'min:2', 'max:180'],
            'second_party_document_type' => ['required', Rule::enum(PartyDocumentType::class)],
            'second_party_document' => ['required', 'string', 'max:40', $this->documentRule('second_party_document_type')],
            'second_party_address' => ['required', 'string', 'min:5', 'max:240'],
            'second_party_city' => ['required', 'string', 'min:2', 'max:120'],
            'second_party_state' => ['required', 'string', 'size:2', 'regex:/^[A-Za-z]{2}$/'],
            'amount' => ['required', 'us_money', function (string $attribute, mixed $value, Closure $fail): void {
                try { $money = Money::fromDecimal((string) $value); } catch (\Throwable) { return; }
                if ($money->minorAmount() <= 0) { $fail('The contract amount must be greater than zero.'); }
                if ($money->minorAmount() > 99_999_999_999) { $fail('The maximum supported amount is $999,999,999.99.'); }
            }],
            'payment_terms' => ['required', 'string', 'min:3', 'max:1200'],
            'service_description' => ['required_if:contract_type,'.ContractType::ServiceAgreement->value, 'nullable', 'string', 'min:5', 'max:4000'],
            'start_date' => ['required_if:contract_type,'.ContractType::ServiceAgreement->value, 'nullable', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'termination_notice_days' => ['required_if:contract_type,'.ContractType::ServiceAgreement->value, 'nullable', 'integer', 'min:0', 'max:365'],
            'asset_description' => ['required_if:contract_type,'.ContractType::GoodsSaleAgreement->value, 'nullable', 'string', 'min:5', 'max:4000'],
            'delivery_date' => ['required_if:contract_type,'.ContractType::GoodsSaleAgreement->value, 'nullable', 'date_format:Y-m-d'],
            'delivery_location' => ['required_if:contract_type,'.ContractType::GoodsSaleAgreement->value, 'nullable', 'string', 'min:3', 'max:240'],
            'jurisdiction_city' => ['required', 'string', 'min:2', 'max:120'],
            'jurisdiction_state' => ['required', 'string', 'size:2', 'regex:/^[A-Za-z]{2}$/'],
            'signing_city' => ['required', 'string', 'min:2', 'max:120'],
            'signing_date' => ['required', 'date_format:Y-m-d'],
            'additional_terms' => ['nullable', 'string', 'max:4000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $template = ContractTemplate::tryFrom((string) $this->input('template_key'));
            $type = ContractType::tryFrom((string) $this->input('contract_type'));
            if ($template !== null && $type !== null && $template->contractType() !== $type) {
                $validator->errors()->add('template_key', 'The selected template does not match the agreement type.');
            }
        });
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'first_party_state.regex' => 'Enter a two-letter U.S. state code.',
            'second_party_state.regex' => 'Enter a two-letter U.S. state code.',
            'jurisdiction_state.regex' => 'Enter a two-letter U.S. state code.',
            'end_date.after_or_equal' => 'The end date cannot be earlier than the start date.',
        ];
    }

    protected function prepareForValidation(): void
    {
        foreach (['first_party_name','first_party_document','first_party_address','first_party_city','second_party_name','second_party_document','second_party_address','second_party_city','amount','payment_terms','service_description','asset_description','delivery_location','jurisdiction_city','signing_city','additional_terms'] as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $this->merge([$field => trim((string) $this->input($field))]);
            }
        }
        foreach (['first_party_state','second_party_state','jurisdiction_state'] as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $this->merge([$field => strtoupper(trim((string) $this->input($field)))]);
            }
        }
    }

    private function documentRule(string $typeField): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) use ($typeField): void {
            $type = PartyDocumentType::tryFrom((string) $this->input($typeField));
            if ($type === PartyDocumentType::Ein) {
                $digits = preg_replace('/\D+/', '', (string) $value) ?? '';
                if (strlen($digits) !== 9 || preg_match('/^(\d)\1{8}$/', $digits) === 1) {
                    $fail('Enter an EIN in the format XX-XXXXXXX. This checks format only, not IRS registration status.');
                }
            } elseif ($type === PartyDocumentType::Other && trim((string) $value) === '') {
                $fail('Enter an identifier.');
            }
        };
    }
}
