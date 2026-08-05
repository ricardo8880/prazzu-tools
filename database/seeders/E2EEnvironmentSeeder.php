<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

final class E2EEnvironmentSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('e2e') || ! config('e2e_environment.enabled')) {
            throw new RuntimeException('O E2EEnvironmentSeeder só pode executar no ambiente e2e isolado.');
        }

        foreach (config('e2e_environment.profiles', []) as $profile) {
            User::query()->updateOrCreate(
                ['email' => $profile['email']],
                [
                    'name' => $profile['name'],
                    'password' => Hash::make($profile['password']),
                    'role' => $profile['role'],
                    'subscription_plan' => $profile['subscription_plan'],
                    'email_verified_at' => now(),
                ],
            );
        }
    }
}
