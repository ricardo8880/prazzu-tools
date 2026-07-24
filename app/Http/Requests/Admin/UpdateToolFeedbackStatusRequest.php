<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Feedback;

use App\Core\Feedback\Enums\ToolFeedbackStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateToolFeedbackStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::enum(ToolFeedbackStatus::class)],
        ];
    }
}
