<?php

use App\Core\Organizations\Enums\OrganizationSubscriptionStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('organization_subscriptions')) {
            Schema::create('organization_subscriptions', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
                $table->string('status', 30)->default(OrganizationSubscriptionStatus::Pending->value)->index();
                $table->unsignedInteger('seat_limit');
                $table->string('billing_provider')->nullable();
                $table->string('billing_reference')->nullable()->unique();
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->timestamps();
                $table->index(['organization_id', 'status']);
            });
        }

        if (! Schema::hasTable('organization_seats')) {
            Schema::create('organization_seats', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('organization_subscription_id')->constrained()->cascadeOnDelete();
                $table->foreignId('organization_member_id')->constrained()->cascadeOnDelete();
                $table->timestamp('assigned_at');
                $table->timestamp('released_at')->nullable();
                $table->timestamps();
                $table->index(['organization_subscription_id', 'released_at'], 'org_seats_subscription_active_index');
                $table->index(['organization_member_id', 'released_at'], 'org_seats_member_active_index');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_seats');
        Schema::dropIfExists('organization_subscriptions');
    }
};
