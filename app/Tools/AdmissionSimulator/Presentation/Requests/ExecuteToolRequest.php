<?php

declare(strict_types=1);

namespace App\Tools\AdmissionSimulator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ExecuteToolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $m = ['required', 'brazilian_money', 'money_min:0'];

        return ['salary' => ['required', 'brazilian_money', 'money_min:0.01'], 'benefits' => $m, 'monthly_burden' => ['required', 'numeric', 'min:0', 'max:200'], 'exam' => $m, 'recruitment' => $m, 'equipment' => $m, 'training' => $m];
    }
}
