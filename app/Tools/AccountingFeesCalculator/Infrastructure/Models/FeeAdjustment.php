<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Infrastructure\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

final class FeeAdjustment extends Model
{
    protected $table = 'accounting_fee_adjustments';

    protected $fillable = [
        'user_id', 'session_key', 'scenario_label', 'index_type', 'reference_period',
        'percentage', 'current_value_cents', 'difference_cents', 'adjusted_value_cents', 'notes',
    ];

    protected function casts(): array
    {
        return ['percentage' => 'decimal:4'];
    }

    public function scopeVisibleTo(Builder $query, int|string|null $userId, string $sessionKey): Builder
    {
        return $query->where(function (Builder $owner) use ($userId, $sessionKey): void {
            if ($userId !== null) {
                $owner->where('user_id', $userId);
            } else {
                $owner->whereNull('user_id')->where('session_key', $sessionKey);
            }
        });
    }

    public function formattedValue(string $attribute): string
    {
        return 'R$ '.number_format(((int) $this->{$attribute}) / 100, 2, ',', '.');
    }
}
