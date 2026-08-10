<?php

declare(strict_types=1);

namespace App\Tools\TurnoverCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ExecuteToolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'admissions' => ['required', 'integer', 'min:0', 'max:10000000'],
            'terminations' => ['required', 'integer', 'min:0', 'max:10000000'],
            'average_headcount' => ['required', 'integer', 'min:1', 'max:10000000'],
        ];
    }
}
