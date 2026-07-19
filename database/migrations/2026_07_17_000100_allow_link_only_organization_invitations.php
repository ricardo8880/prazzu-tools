<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organization_invitations', function (Blueprint $table): void {
            $table->string('email')->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('organization_invitations')->whereNull('email')->delete();

        Schema::table('organization_invitations', function (Blueprint $table): void {
            $table->string('email')->nullable(false)->change();
        });
    }
};
