<?php

declare(strict_types=1);

namespace App\Http\Requests\Analytics;

use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class TrackToolEventRequest extends FormRequest
{
    /** @return list<string> */
    public static function publishableEvents(): array
    {
        return [
            AnalyticsEventName::ToolStarted->value,
            AnalyticsEventName::ToolStepChanged->value,
            AnalyticsEventName::ToolFieldCompleted->value,
            AnalyticsEventName::ToolValidationError->value,
            AnalyticsEventName::ToolCalculationStarted->value,
            AnalyticsEventName::ToolCalculationExecuted->value,
            AnalyticsEventName::ToolResultViewed->value,
            AnalyticsEventName::ToolTimeSpent->value,
            AnalyticsEventName::ToolResultExported->value,
            AnalyticsEventName::ToolShared->value,
            AnalyticsEventName::ToolAbandoned->value,
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, list<mixed>> */
    public function rules(): array
    {
        return [
            'tool' => ['required', 'string', 'max:120'],
            'event' => ['required', 'string', Rule::in(self::publishableEvents())],
            'schema_version' => ['sometimes', 'integer', 'min:1', 'max:10'],
            'metadata' => ['sometimes', 'array'],
            'metadata.journey_id' => ['sometimes', 'string', 'max:80', 'regex:/^[a-zA-Z0-9._-]+$/'],
            'metadata.form' => ['sometimes', 'string', 'max:80', 'regex:/^[a-z0-9]+(?:[._-][a-z0-9]+)*$/'],
            'metadata.step' => ['sometimes', 'string', 'max:80', 'regex:/^[a-z0-9]+(?:[._-][a-z0-9]+)*$/'],
            'metadata.field' => ['sometimes', 'string', 'max:80', 'regex:/^[a-z0-9]+(?:[._-][a-z0-9]+)*$/'],
            'metadata.action' => ['sometimes', 'string', 'max:80', 'regex:/^[a-z0-9]+(?:[._-][a-z0-9]+)*$/'],
            'metadata.completion_percentage' => ['sometimes', 'integer', 'min:0', 'max:100'],
            'metadata.filled_fields' => ['sometimes', 'integer', 'min:0', 'max:1000'],
            'metadata.total_fields' => ['sometimes', 'integer', 'min:0', 'max:1000'],
            'metadata.validation_error' => ['sometimes', 'string', 'max:120', 'regex:/^[a-z0-9]+(?:[._-][a-z0-9]+)*$/'],
            'metadata.execution_time_ms' => ['sometimes', 'integer', 'min:0', 'max:3600000'],
            'metadata.calculation_success' => ['sometimes', 'boolean'],
            'metadata.time_spent_seconds' => ['sometimes', 'integer', 'min:0', 'max:86400'],
            'metadata.abandoned_after_seconds' => ['sometimes', 'integer', 'min:0', 'max:86400'],
            'metadata.export_format' => ['sometimes', 'string', 'max:30', 'regex:/^[a-z0-9_-]+$/'],
            'metadata.share_method' => ['sometimes', 'string', 'max:30', 'regex:/^[a-z0-9_-]+$/'],
            // Compatibilidade temporária do contrato anterior.
            'seconds' => ['sometimes', 'integer', 'min:0', 'max:86400'],
        ];
    }
}
