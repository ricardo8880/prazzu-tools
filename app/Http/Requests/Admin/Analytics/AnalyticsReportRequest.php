<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Analytics;

use Illuminate\Validation\Rule;

final class AnalyticsReportRequest extends AnalyticsDashboardRequest
{
    public function rules(): array
    {
        return parent::rules() + [
            'channel' => ['nullable', 'string', 'max:80'], 'category' => ['nullable', 'string', 'max:100'],
            'author_id' => ['nullable', 'integer', 'min:1'], 'tool' => ['nullable', 'string', 'max:120'],
            'source' => ['nullable', 'string', 'max:120'], 'city' => ['nullable', 'string', 'max:120'],
            'region' => ['nullable', 'string', 'max:120'], 'device_type' => ['nullable', 'string', 'max:30'],
            'operating_system' => ['nullable', 'string', 'max:80'], 'user_id' => ['nullable', 'integer', 'min:1'],
            'event_name' => ['nullable', 'string', 'max:120'], 'format' => ['nullable', Rule::in(['csv', 'excel', 'pdf', 'markdown', 'json', 'package', 'package_summary'])],
        ];
    }

    /** @return array<string, mixed> */
    public function filters(): array
    {
        return collect($this->validated())->only(['channel', 'category', 'author_id', 'tool', 'source', 'city', 'region', 'device_type', 'operating_system', 'user_id', 'event_name'])->filter(fn ($value) => $value !== null && $value !== '')->all();
    }
}
