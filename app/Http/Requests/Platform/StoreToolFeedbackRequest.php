<?php

declare(strict_types=1);

namespace App\Http\Requests\Platform;

use App\Core\Feedback\Enums\ToolFeedbackType;
use App\Core\Tools\ToolRegistry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreToolFeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'tool_slug' => [
                'required',
                'string',
                'max:120',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::in(array_keys(app(ToolRegistry::class)->modules())),
            ],
            'type' => ['required', Rule::enum(ToolFeedbackType::class)],
            'message' => ['required', 'string', 'max:5000'],
            'attempted_action' => ['nullable', 'string', 'max:2000'],
            'path' => ['required', 'string', 'max:512', 'starts_with:/'],
            'url' => ['required', 'url', 'max:4096'],
            'route_name' => ['nullable', 'string', 'max:255'],
            'page_title' => ['nullable', 'string', 'max:255'],
        ];
    }
}
