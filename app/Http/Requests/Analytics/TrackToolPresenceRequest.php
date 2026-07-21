<?php

namespace App\Http\Requests\Analytics;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class TrackToolPresenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'presence_id' => ['required', 'uuid'],
            'tool' => ['required', 'string', 'max:120'],
            'action' => ['required', Rule::in(['heartbeat', 'leave'])],
        ];
    }
}
