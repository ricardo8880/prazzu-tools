<?php

namespace App\Http\Requests\Admin\Analytics;

use App\Core\Analytics\Domain\ValueObjects\AnalyticsPeriod;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class AnalyticsDashboardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'period' => ['nullable', Rule::in(['today', 'yesterday', '7', '30', '90', 'custom'])],
            'start' => ['nullable', 'required_if:period,custom', 'date_format:Y-m-d'],
            'end' => ['nullable', 'required_if:period,custom', 'date_format:Y-m-d', 'after_or_equal:start'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($this->input('period') !== 'custom' || $validator->errors()->isNotEmpty()) {
                return;
            }

            $start = CarbonImmutable::parse((string) $this->input('start'))->startOfDay();
            $end = CarbonImmutable::parse((string) $this->input('end'))->endOfDay();

            if ($start->diffInDays($end) > 365) {
                $validator->errors()->add('end', 'O período máximo do dashboard é de 366 dias.');
            }
        });
    }

    public function selectedPeriod(): string
    {
        return (string) $this->validated('period', '7');
    }

    public function period(): AnalyticsPeriod
    {
        return match ($this->validated('period', '7')) {
            'today' => AnalyticsPeriod::lastDays(1),
            'yesterday' => new AnalyticsPeriod(now()->toImmutable()->subDay()->startOfDay(), now()->toImmutable()->subDay()->endOfDay()),
            '30' => AnalyticsPeriod::lastDays(30),
            '90' => AnalyticsPeriod::lastDays(90),
            'custom' => AnalyticsPeriod::between((string) $this->validated('start'), (string) $this->validated('end')),
            default => AnalyticsPeriod::lastDays(7),
        };
    }
}
