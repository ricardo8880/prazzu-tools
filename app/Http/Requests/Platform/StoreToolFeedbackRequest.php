<?php

declare(strict_types=1);

namespace App\Http\Requests\Platform;

use App\Core\Feedback\Enums\ToolFeedbackType;
use App\Core\Feedback\Enums\ToolResolution;
use App\Core\Feedback\Enums\ToolResolutionReason;
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
            'feedback_kind' => ['nullable', Rule::in(['qualitative', 'resolution'])],
            'type' => [Rule::requiredIf(fn (): bool => $this->input('feedback_kind', 'qualitative') !== 'resolution'), 'nullable', Rule::enum(ToolFeedbackType::class)],
            'message' => [Rule::requiredIf(fn (): bool => $this->input('feedback_kind', 'qualitative') !== 'resolution'), 'nullable', 'string', 'max:5000'],
            'attempted_action' => ['nullable', 'string', 'max:2000'],
            'resolution' => [Rule::requiredIf(fn (): bool => $this->input('feedback_kind') === 'resolution'), 'nullable', Rule::enum(ToolResolution::class)],
            'reason' => [
                Rule::requiredIf(fn (): bool => $this->input('feedback_kind') === 'resolution' && in_array($this->input('resolution'), [ToolResolution::Partially->value, ToolResolution::No->value], true)),
                'nullable',
                Rule::enum(ToolResolutionReason::class),
            ],
            'comment' => ['nullable', 'string', 'max:1000'],
            'path' => ['required', 'string', 'max:512', 'starts_with:/'],
            'url' => ['required', 'url', 'max:4096'],
            'route_name' => ['nullable', 'string', 'max:255'],
            'page_title' => ['nullable', 'string', 'max:255'],
        ];
    }
}
