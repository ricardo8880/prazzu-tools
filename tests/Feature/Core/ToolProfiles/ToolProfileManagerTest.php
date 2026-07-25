<?php

declare(strict_types=1);

namespace Tests\Feature\Core\ToolProfiles;

use App\Core\ToolProfiles\Services\ToolProfileManager;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ToolProfileManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_profiles_are_reusable_and_strictly_scoped_to_the_owner(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $manager = app(ToolProfileManager::class);

        $company = $manager->storeCompany($owner->id, ['name' => 'Empresa Alfa']);
        $employee = $manager->storeEmployee($owner->id, [
            'company_profile_id' => $company->id,
            'name' => 'Ana',
            'document' => '12345678901',
            'defaults' => ['salary' => '5000,00'],
        ]);

        self::assertSame('Empresa Alfa', $manager->findEmployeeOwned($employee->id, $owner->id)->company?->name);
        self::assertSame('5000,00', $manager->findEmployeeOwned($employee->id, $owner->id)->defaults['salary']);

        $this->expectException(ModelNotFoundException::class);
        $manager->findEmployeeOwned($employee->id, $other->id);
    }
}
