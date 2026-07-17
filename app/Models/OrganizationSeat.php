<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class OrganizationSeat extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_subscription_id', 'organization_member_id',
        'assigned_at', 'released_at',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(OrganizationSubscription::class, 'organization_subscription_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(OrganizationMember::class, 'organization_member_id');
    }

    public function isActive(): bool
    {
        return $this->released_at === null;
    }

    protected function casts(): array
    {
        return [
            'assigned_at' => 'immutable_datetime',
            'released_at' => 'immutable_datetime',
        ];
    }
}
