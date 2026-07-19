<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'subscription_plan')) {
            DB::table('users')
                ->where('subscription_plan', 'premium')
                ->update(['subscription_plan' => 'plus']);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'subscription_plan')) {
            DB::table('users')
                ->where('subscription_plan', 'plus')
                ->update(['subscription_plan' => 'premium']);
        }
    }
};
