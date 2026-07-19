<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Infrastructure\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

final class AccountingFeeCalculation extends Model
{
    protected $table = 'accounting_fee_calculations';

    protected $fillable = [
        'user_id', 'session_key', 'input', 'result', 'is_favorite',
    ];

    protected function casts(): array
    {
        return [
            'input' => 'array',
            'result' => 'array',
            'is_favorite' => 'boolean',
        ];
    }

    public function scopeVisibleTo(Builder $query, int|string|null $userId, string $sessionKey): Builder
    {
        return $query->when(
            $userId !== null,
            fn (Builder $builder): Builder => $builder->where('user_id', $userId),
            fn (Builder $builder): Builder => $builder->whereNull('user_id')->where('session_key', $sessionKey),
        );
    }

    public function recommendedFee(): string
    {
        return (string) data_get($this->result, 'recommended_fee', '—');
    }
}
