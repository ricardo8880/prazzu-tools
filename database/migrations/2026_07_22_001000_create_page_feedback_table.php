<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_feedback', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id')->nullable()->index();
            $table->string('path', 512);
            $table->text('url');
            $table->string('page_title')->nullable();
            $table->unsignedTinyInteger('rating')->index();
            $table->text('comment')->nullable();
            $table->string('user_agent', 1024)->nullable();
            $table->timestamps();

            $table->index(['path', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_feedback');
    }
};
