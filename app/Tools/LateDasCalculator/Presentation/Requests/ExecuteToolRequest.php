<?php

declare(strict_types=1);

namespace App\Tools\LateDasCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ExecuteToolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['principal' => ['required', 'brazilian_money', 'money_min:0.01'], 'due_date' => ['required', 'date'], 'payment_date' => ['required', 'date'], 'accumulated_selic' => ['required', 'numeric', 'min:0', 'max:500']];
    }
}
