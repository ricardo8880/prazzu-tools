<?php

namespace App\Http\Requests\Platform;

use Illuminate\Foundation\Http\FormRequest;

final class SuggestToolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'problem' => ['required', 'string', 'min:20', 'max:2000'],
        ];
    }
}
