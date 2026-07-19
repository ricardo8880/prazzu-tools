<?php

namespace App\Http\Requests\Admin\Analytics;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class SeoMetricSnapshotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'metric_date' => ['required', 'date'],
            'source' => ['required', 'string', 'max:40'],
            'search_type' => ['required', Rule::in(['web', 'image', 'video', 'news', 'discover'])],
            'device' => ['required', Rule::in(['all', 'desktop', 'mobile', 'tablet'])],
            'country_code' => ['nullable', 'string', 'size:2'],
            'clicks' => ['required', 'integer', 'min:0'],
            'impressions' => ['required', 'integer', 'min:0'],
            'average_position' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'discover_clicks' => ['required', 'integer', 'min:0'],
            'discover_impressions' => ['required', 'integer', 'min:0'],
            'news_clicks' => ['required', 'integer', 'min:0'],
            'news_impressions' => ['required', 'integer', 'min:0'],
            'rich_result_clicks' => ['required', 'integer', 'min:0'],
            'rich_result_impressions' => ['required', 'integer', 'min:0'],
        ];
    }
}
