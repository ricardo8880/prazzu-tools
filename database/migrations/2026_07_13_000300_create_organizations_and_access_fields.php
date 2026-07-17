<?php

use App\Core\Organizations\Enums\OrganizationInvitationStatus;
use App\Core\Organizations\Enums\OrganizationMemberRole;
use App\Core\Organizations\Enums\OrganizationMemberStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addAccessFieldsToUsers();
        $this->createOrganizations();
        $this->createOrganizationMembers();
        $this->migrateLegacyOrganizationUsers();
        $this->createOrganizationInvitations();
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_invitations');
        Schema::dropIfExists('organization_members');
        Schema::dropIfExists('organization_user');
        Schema::dropIfExists('organizations');

        Schema::table('users', function (Blueprint $table): void {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }

            if (Schema::hasColumn('users', 'subscription_plan')) {
                $table->dropColumn('subscription_plan');
            }
        });
    }

    private function addAccessFieldsToUsers(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role', 30)->default('user')->index();
            }

            if (! Schema::hasColumn('users', 'subscription_plan')) {
                $table->string('subscription_plan', 30)->default('free')->index();
            }
        });
    }

    private function createOrganizations(): void
    {
        if (! Schema::hasTable('organizations')) {
            Schema::create('organizations', function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->foreignId('owner_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });

            return;
        }

        if (! Schema::hasColumn('organizations', 'owner_user_id')) {
            Schema::table('organizations', function (Blueprint $table): void {
                $table->foreignId('owner_user_id')->nullable()->after('slug')->constrained('users')->nullOnDelete();
            });
        }
    }

    private function createOrganizationMembers(): void
    {
        if (Schema::hasTable('organization_members')) {
            return;
        }

        Schema::create('organization_members', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role', 30)->default(OrganizationMemberRole::Member->value);
            $table->string('status', 30)->default(OrganizationMemberStatus::Active->value);
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('left_at')->nullable();
            $table->timestamps();

            $table->unique(['organization_id', 'user_id']);
            $table->index(['organization_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    private function migrateLegacyOrganizationUsers(): void
    {
        if (! Schema::hasTable('organization_user')) {
            return;
        }

        DB::table('organization_user')
            ->orderBy('organization_id')
            ->orderBy('user_id')
            ->get()
            ->each(function (object $membership): void {
                DB::table('organization_members')->updateOrInsert(
                    [
                        'organization_id' => $membership->organization_id,
                        'user_id' => $membership->user_id,
                    ],
                    [
                        'role' => $membership->role === 'owner'
                            ? OrganizationMemberRole::Owner->value
                            : OrganizationMemberRole::Member->value,
                        'status' => OrganizationMemberStatus::Active->value,
                        'joined_at' => $membership->created_at ?? now(),
                        'created_at' => $membership->created_at ?? now(),
                        'updated_at' => $membership->updated_at ?? now(),
                    ],
                );
            });

        Schema::drop('organization_user');
    }

    private function createOrganizationInvitations(): void
    {
        if (Schema::hasTable('organization_invitations')) {
            return;
        }

        Schema::create('organization_invitations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->string('role', 30)->default(OrganizationMemberRole::Member->value);
            $table->string('status', 30)->default(OrganizationInvitationStatus::Pending->value);
            $table->string('token', 64)->unique();
            $table->foreignId('invited_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('accepted_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'status']);
            $table->index(['email', 'status']);
        });
    }
};
