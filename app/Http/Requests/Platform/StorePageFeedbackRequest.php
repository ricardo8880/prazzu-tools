<?php

namespace App\Http\Requests\Platform;

use Illuminate\Foundation\Http\FormRequest;

final class StorePageFeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:2000'],
            'path' => ['required', 'string', 'max:512', 'starts_with:/'],
            'url' => ['required', 'url', 'max:4096'],
            'page_title' => ['nullable', 'string', 'max:255'],
        ];
    }
}
