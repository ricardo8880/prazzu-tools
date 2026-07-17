<?php

namespace App\Models;

use App\Blog\Models\BlogPost;
use App\Core\Access\Enums\AccountRole;
use App\Core\Access\Enums\SubscriptionPlan;
use App\Core\Identity\Notifications\PrazzuResetPassword;
use App\Core\Organizations\Contracts\EnterpriseAccessResolver;
use App\Core\Identity\Notifications\PrazzuVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'subscription_plan',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new PrazzuVerifyEmail);
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new PrazzuResetPassword($token));
    }

    public function blogPosts(): HasMany
    {
        return $this->hasMany(BlogPost::class, 'author_id');
    }

    public function ownedOrganizations(): HasMany
    {
        return $this->hasMany(Organization::class, 'owner_user_id');
    }

    public function organizationMemberships(): HasMany
    {
        return $this->hasMany(OrganizationMember::class);
    }

    public function organizationInvitationsSent(): HasMany
    {
        return $this->hasMany(OrganizationInvitation::class, 'invited_by_user_id');
    }

    public function hasPremiumAccess(): bool
    {
        return $this->subscription_plan->grantsPremiumTools()
            || app(EnterpriseAccessResolver::class)->grantsPlusAccessTo($this->getKey());
    }

    public function effectiveSubscriptionPlan(): SubscriptionPlan
    {
        return $this->hasPremiumAccess() ? SubscriptionPlan::Premium : SubscriptionPlan::Free;
    }

    public function isInternalAdministrator(): bool
    {
        return $this->role->isInternal();
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => AccountRole::class,
            'subscription_plan' => SubscriptionPlan::class,
        ];
    }
}
