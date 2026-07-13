<?php

namespace App\Http\Requests\Platform;

use Illuminate\Foundation\Http\FormRequest;

final class NewsletterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email:rfc', 'max:255'],
        ];
    }
}
