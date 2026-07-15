<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { Schema::create('analytics_insights', function(Blueprint $table): void { $table->id(); $table->string('fingerprint',64)->unique(); $table->string('type',30)->index(); $table->string('severity',20)->index(); $table->string('title',180); $table->text('message'); $table->text('recommendation')->nullable(); $table->string('subject_type',50)->nullable()->index(); $table->string('subject_slug',180)->nullable()->index(); $table->string('metric_name',80)->nullable(); $table->decimal('current_value',18,4)->nullable(); $table->decimal('previous_value',18,4)->nullable(); $table->decimal('change_percent',10,2)->nullable(); $table->string('status',20)->default('open')->index(); $table->json('metadata')->nullable(); $table->date('period_start')->index(); $table->date('period_end')->index(); $table->timestamp('generated_at')->index(); $table->timestamp('resolved_at')->nullable(); $table->timestamps(); }); }
 public function down(): void { Schema::dropIfExists('analytics_insights'); }
};
