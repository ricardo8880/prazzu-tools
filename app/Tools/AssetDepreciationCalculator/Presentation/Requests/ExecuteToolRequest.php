<?php

declare(strict_types=1);

namespace App\Tools\AssetDepreciationCalculator\Presentation\Requests;

use App\Core\Money\Money;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Throwable;

final class ExecuteToolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'asset_name' => ['required', 'string', 'max:120'],
            'asset_value' => ['required', 'brazilian_money', 'money_min:0.01'],
            'useful_life_years' => ['required', 'integer', 'min:1', 'max:100'],
            'method' => ['required', 'in:linear,declining_balance,sum_of_years_digits'],
            'assets' => ['nullable', 'array', 'max:20'],
            'assets.*.name' => ['nullable', 'string', 'max:120'],
            'assets.*.value' => ['nullable', 'brazilian_money', 'money_min:0'],
            'assets.*.useful_life_years' => ['nullable', 'integer', 'min:1', 'max:100'],
            'assets.*.method' => ['nullable', 'in:linear,declining_balance,sum_of_years_digits'],
            'registered_asset_ids' => ['nullable','array','max:20'],
            'registered_asset_ids.*' => ['integer','min:1'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            foreach ((array) $this->input('assets', []) as $index => $asset) {
                $name = trim((string) ($asset['name'] ?? ''));
                $value = trim((string) ($asset['value'] ?? ''));

                try {
                    $minor = $value === '' ? 0 : Money::fromDecimal($value)->minorAmount();
                } catch (Throwable) {
                    continue;
                }

                if ($name === '' && $minor <= 0) {
                    continue;
                }
                if ($name === '') {
                    $validator->errors()->add("assets.$index.name", 'Informe o nome do ativo adicional.');
                }
                if ($minor <= 0) {
                    $validator->errors()->add("assets.$index.value", 'Informe um valor maior que zero para o ativo adicional.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'asset_name.required' => 'Informe o bem que será depreciado.',
            'asset_value.required' => 'Informe o valor do bem.',
            'useful_life_years.required' => 'Informe a vida útil em anos.',
        ];
    }
}
