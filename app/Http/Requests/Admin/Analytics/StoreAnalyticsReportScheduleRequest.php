<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Analytics;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreAnalyticsReportScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'], 'frequency' => ['required', Rule::in(['daily', 'weekly', 'monthly'])],
            'format' => ['required', Rule::in(['csv', 'excel', 'pdf'])], 'period' => ['required', Rule::in(['7', '30', '90'])],
            'channel' => ['nullable', 'string', 'max:80'], 'category' => ['nullable', 'string', 'max:100'],
            'author_id' => ['nullable', 'integer', 'min:1'], 'tool' => ['nullable', 'string', 'max:120'],
            'source' => ['nullable', 'string', 'max:120'], 'city' => ['nullable', 'string', 'max:120'],
            'region' => ['nullable', 'string', 'max:120'], 'device_type' => ['nullable', 'string', 'max:30'],
            'operating_system' => ['nullable', 'string', 'max:80'], 'user_id' => ['nullable', 'integer', 'min:1'],
            'event_name' => ['nullable', 'string', 'max:120'],
        ];
    }
}
