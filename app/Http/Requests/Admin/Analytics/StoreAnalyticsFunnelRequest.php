<?php

namespace App\Http\Requests\Admin\Analytics;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreAnalyticsFunnelRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'identity_type' => ['required', Rule::in(['visitor', 'session', 'user'])],
            'steps' => ['required', 'string', function (string $attribute, mixed $value, \Closure $fail): void {
                if (count($this->parsedSteps()) < 2) $fail('Informe ao menos duas etapas válidas.');
            }],
        ];
    }

    /** @return list<array{name:string,event_names:list<string>}> */
    public function parsedSteps(): array
    {
        return collect(preg_split('/\R/', (string) $this->input('steps')) ?: [])
            ->map(function (string $line): ?array {
                [$name, $events] = array_pad(explode('|', $line, 2), 2, '');
                $eventNames = collect(explode(',', $events))->map(fn ($v) => trim($v))->filter()->unique()->values()->all();
                return trim($name) !== '' && $eventNames !== [] ? ['name' => trim($name), 'event_names' => $eventNames] : null;
            })->filter()->values()->all();
    }
}
