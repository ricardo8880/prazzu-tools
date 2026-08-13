<?php

declare(strict_types=1);

namespace App\Tools\AssetDepreciationCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'value' => ['required', 'brazilian_money', 'money_min:0.01'],
            'useful_life_years' => ['required', 'integer', 'min:1', 'max:100'],
            'method' => ['required', 'in:linear,declining_balance,sum_of_years_digits'],
        ];
    }
}
