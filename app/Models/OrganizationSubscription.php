<?php

namespace App\Models;

use App\Core\Organizations\Enums\OrganizationSubscriptionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class OrganizationSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id', 'status', 'seat_limit', 'billing_provider',
        'billing_reference', 'starts_at', 'ends_at',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function seats(): HasMany
    {
        return $this->hasMany(OrganizationSeat::class);
    }

    public function grantsPlusAccess(): bool
    {
        return $this->status->grantsPlusAccess()
            && ($this->starts_at === null || $this->starts_at->isPast())
            && ($this->ends_at === null || $this->ends_at->isFuture());
    }

    protected function casts(): array
    {
        return [
            'status' => OrganizationSubscriptionStatus::class,
            'seat_limit' => 'integer',
            'starts_at' => 'immutable_datetime',
            'ends_at' => 'immutable_datetime',
        ];
    }
}
