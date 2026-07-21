<?php

declare(strict_types=1);

namespace App\Tools\FederalPaymentGuideGenerator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CalculateGuideRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    /** @return array<string,array<int,mixed>> */
    public function rules(): array
    {
        return [
            'guide_type' => ['required', Rule::in(['darf', 'gps'])],
            'revenue_code' => ['required', 'digits:4'],
            'principal' => ['required', 'regex:/^\s*(?:R\$\s*)?\d{1,12}(?:\.\d{3})*(?:,\d{1,2})?\s*$/'],
            'due_date' => ['required', 'date_format:Y-m-d'],
            'payment_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:due_date'],
            'selic_accumulated_percent' => ['nullable', 'regex:/^\d{1,3}(?:[\.,]\d{1,6})?$/'],
            'confirm_official_check' => ['accepted'],
        ];
    }

    /** @return array<string,string> */
    public function messages(): array
    {
        return [
            'principal.regex' => 'Informe o principal no formato 1.000,00.',
            'payment_date.after_or_equal' => 'A data prevista de pagamento não pode ser anterior ao vencimento.',
            'selic_accumulated_percent.regex' => 'Informe a Selic acumulada como percentual, por exemplo 1,25.',
            'confirm_official_check.accepted' => 'Confirme que os dados serão conferidos no sistema oficial.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('selic_accumulated_percent')) {
            $this->merge(['selic_accumulated_percent' => str_replace(',', '.', (string) $this->input('selic_accumulated_percent'))]);
        }
    }
}
