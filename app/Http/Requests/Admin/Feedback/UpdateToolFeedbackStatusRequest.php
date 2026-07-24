<?php

namespace App\Http\Requests\Admin\Feedback;

use App\Core\Feedback\Enums\ToolFeedbackStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateToolFeedbackStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                Rule::enum(ToolFeedbackStatus::class),
            ],
        ];
    }
}
