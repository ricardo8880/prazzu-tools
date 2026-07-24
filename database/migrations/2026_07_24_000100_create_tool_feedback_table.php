<?php

use App\Core\Feedback\Enums\ToolFeedbackStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tool_feedback', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id')->nullable()->index();
            $table->string('tool_slug', 120)->index();
            $table->string('tool_name');
            $table->string('tool_version', 32);
            $table->string('type', 40)->index();
            $table->string('status', 40)->default(ToolFeedbackStatus::New->value)->index();
            $table->text('message');
            $table->text('attempted_action')->nullable();
            $table->string('path', 512);
            $table->text('url');
            $table->json('context')->nullable();
            $table->string('user_agent', 1024)->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['tool_slug', 'status', 'created_at']);
            $table->index(['type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tool_feedback');
    }
};
