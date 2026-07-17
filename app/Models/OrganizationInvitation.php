<?php

namespace App\Models;

use App\Core\Organizations\Enums\OrganizationInvitationStatus;
use App\Core\Organizations\Enums\OrganizationMemberRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class OrganizationInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'email',
        'role',
        'status',
        'token',
        'invited_by_user_id',
        'accepted_by_user_id',
        'expires_at',
        'accepted_at',
        'revoked_at',
    ];

    protected $hidden = [
        'token',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by_user_id');
    }

    public function acceptedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accepted_by_user_id');
    }

    public function canBeAccepted(): bool
    {
        return $this->status->canBeAccepted()
            && $this->accepted_at === null
            && $this->revoked_at === null
            && $this->expires_at->isFuture();
    }

    protected function casts(): array
    {
        return [
            'role' => OrganizationMemberRole::class,
            'status' => OrganizationInvitationStatus::class,
            'expires_at' => 'immutable_datetime',
            'accepted_at' => 'immutable_datetime',
            'revoked_at' => 'immutable_datetime',
        ];
    }
}
