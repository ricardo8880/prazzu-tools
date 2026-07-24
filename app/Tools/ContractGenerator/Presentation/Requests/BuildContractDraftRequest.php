<?php

declare(strict_types=1);

namespace App\Tools\ContractGenerator\Presentation\Requests;

use App\Core\Identifiers\Cnpj;
use App\Core\Identifiers\Cpf;
use App\Core\Money\Money;
use App\Tools\ContractGenerator\Domain\Enums\ContractType;
use App\Tools\ContractGenerator\Domain\Enums\PartyDocumentType;
use Closure;
use DateTimeImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

final class BuildContractDraftRequest extends FormRequest
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
            'first_party_name' => ['required', 'string', 'min:2', 'max:180'],
            'first_party_document_type' => ['required', Rule::enum(PartyDocumentType::class)],
            'first_party_document' => ['required', 'string', 'max:18', $this->documentRule('first_party_document_type')],
            'first_party_address' => ['required', 'string', 'min:5', 'max:240'],
            'first_party_city' => ['required', 'string', 'min:2', 'max:120'],
            'first_party_state' => ['required', 'string', 'size:2', 'regex:/^[A-Za-z]{2}$/'],
            'second_party_name' => ['required', 'string', 'min:2', 'max:180'],
            'second_party_document_type' => ['required', Rule::enum(PartyDocumentType::class)],
            'second_party_document' => ['required', 'string', 'max:18', $this->documentRule('second_party_document_type')],
            'second_party_address' => ['required', 'string', 'min:5', 'max:240'],
            'second_party_city' => ['required', 'string', 'min:2', 'max:120'],
            'second_party_state' => ['required', 'string', 'size:2', 'regex:/^[A-Za-z]{2}$/'],
            'amount' => [
                'required',
                'regex:/^\s*(?:R\$\s*)?\d{1,12}(?:\.\d{3})*(?:,\d{1,2})?\s*$/',
                function (string $attribute, mixed $value, Closure $fail): void {
                    try {
                        $money = Money::fromDecimal((string) $value);
                    } catch (\Throwable) {
                        return;
                    }

                    if ($money->minorAmount() <= 0) {
                        $fail('O valor do contrato deve ser maior que zero.');
                    } elseif ($money->minorAmount() > 99_999_999_999) {
                        $fail('O valor máximo suportado é R$ 999.999.999,99.');
                    }
                },
            ],
            'payment_terms' => ['required', 'string', 'min:3', 'max:1200'],
            'service_description' => ['required_if:contract_type,'.ContractType::ServiceProvision->value, 'nullable', 'string', 'min:5', 'max:4000'],
            'start_date' => ['required_if:contract_type,'.ContractType::ServiceProvision->value, 'nullable', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'termination_notice_days' => ['required_if:contract_type,'.ContractType::ServiceProvision->value, 'nullable', 'integer', 'min:0', 'max:365'],
            'asset_description' => ['required_if:contract_type,'.ContractType::MovableAssetSale->value, 'nullable', 'string', 'min:5', 'max:4000'],
            'delivery_date' => ['required_if:contract_type,'.ContractType::MovableAssetSale->value, 'nullable', 'date_format:Y-m-d'],
            'delivery_location' => ['required_if:contract_type,'.ContractType::MovableAssetSale->value, 'nullable', 'string', 'min:3', 'max:240'],
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
            if ($this->input('contract_type') !== ContractType::ServiceProvision->value) {
                return;
            }

            $start = $this->input('start_date');
            $end = $this->input('end_date');
            if (! is_string($start) || ! is_string($end) || $start === '' || $end === '') {
                return;
            }

            try {
                $startDate = new DateTimeImmutable($start);
                $endDate = new DateTimeImmutable($end);
            } catch (\Throwable) {
                return;
            }

            if ($endDate > $startDate->modify('+4 years')) {
                $validator->errors()->add('end_date', 'A prestação de serviços não pode ser convencionada por prazo superior a quatro anos.');
            }
        });
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'amount.regex' => 'Informe o valor no formato 1.000,00.',
            'first_party_state.regex' => 'Informe a UF com duas letras.',
            'second_party_state.regex' => 'Informe a UF com duas letras.',
            'jurisdiction_state.regex' => 'Informe a UF com duas letras.',
            'end_date.after_or_equal' => 'A data final não pode ser anterior à data inicial.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $trimFields = [
            'first_party_name', 'first_party_document', 'first_party_address', 'first_party_city',
            'second_party_name', 'second_party_document', 'second_party_address', 'second_party_city',
            'amount', 'payment_terms', 'service_description', 'asset_description', 'delivery_location',
            'jurisdiction_city', 'signing_city', 'additional_terms',
        ];

        foreach ($trimFields as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $this->merge([$field => trim((string) $this->input($field))]);
            }
        }

        foreach (['first_party_state', 'second_party_state', 'jurisdiction_state'] as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $this->merge([$field => strtoupper(trim((string) $this->input($field)))]);
            }
        }
    }

    private function documentRule(string $typeField): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) use ($typeField): void {
            $type = PartyDocumentType::tryFrom((string) $this->input($typeField));
            $valid = match ($type) {
                PartyDocumentType::Cpf => Cpf::isValid((string) $value),
                PartyDocumentType::Cnpj => Cnpj::isValid((string) $value),
                null => false,
            };

            if (! $valid) {
                $fail('Informe um CPF ou CNPJ válido de acordo com o tipo selecionado.');
            }
        };
    }
}
