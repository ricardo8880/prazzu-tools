<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Infrastructure\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

final class AccountingClient extends Model
{
    protected $table = 'accounting_fee_clients';

    protected $fillable = [
        'user_id',
        'session_key',
        'company_name',
        'document',
        'contact_name',
        'email',
        'phone',
        'monthly_fee_cents',
        'proposal_status',
        'contract_status',
        'pipeline_status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'monthly_fee_cents' => 'integer',
        ];
    }

    public function scopeVisibleTo(Builder $query, ?int $userId, string $sessionKey): Builder
    {
        return $query->when(
            $userId !== null,
            fn (Builder $builder): Builder => $builder->where('user_id', $userId),
            fn (Builder $builder): Builder => $builder->whereNull('user_id')->where('session_key', $sessionKey),
        );
    }

    public function formattedMonthlyFee(): string
    {
        return 'R$ '.number_format($this->monthly_fee_cents / 100, 2, ',', '.');
    }
}
