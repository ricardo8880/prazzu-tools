<?php

namespace App\Models;

use App\Core\Organizations\Enums\OrganizationMemberRole;
use App\Core\Organizations\Enums\OrganizationMemberStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class OrganizationMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'user_id',
        'role',
        'status',
        'joined_at',
        'left_at',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function seats(): HasMany
    {
        return $this->hasMany(OrganizationSeat::class);
    }

    public function isActive(): bool
    {
        return $this->status === OrganizationMemberStatus::Active && $this->left_at === null;
    }

    protected function casts(): array
    {
        return [
            'role' => OrganizationMemberRole::class,
            'status' => OrganizationMemberStatus::class,
            'joined_at' => 'immutable_datetime',
            'left_at' => 'immutable_datetime',
        ];
    }
}
