<?php

namespace App\Http\Requests\Analytics;

use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class TrackToolEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['tool' => ['required', 'string', 'max:120'], 'event' => ['required', Rule::in([AnalyticsEventName::ToolCalculationStarted->value, AnalyticsEventName::ToolTimeSpent->value])], 'seconds' => ['nullable', 'integer', 'min:0', 'max:86400']];
    }
}
