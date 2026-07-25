<?php

declare(strict_types=1);

namespace App\Core\ToolProfiles\Services;

use App\Core\ToolProfiles\Models\ToolCompanyProfile;
use App\Core\ToolProfiles\Models\ToolEmployeeProfile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

final class ToolProfileManager
{
    /** @return Collection<int, ToolCompanyProfile> */
    public function companies(int $userId): Collection
    {
        return ToolCompanyProfile::query()
            ->where('user_id', $userId)
            ->withCount('employees')
            ->orderBy('name')
            ->get();
    }

    /** @return Collection<int, ToolEmployeeProfile> */
    public function employees(int $userId, ?int $companyId = null): Collection
    {
        return ToolEmployeeProfile::query()
            ->where('user_id', $userId)
            ->when($companyId !== null, static fn ($query) => $query->where('company_profile_id', $companyId))
            ->with('company')
            ->orderBy('name')
            ->get();
    }

    /** @param array<string, mixed> $data */
    public function storeCompany(int $userId, array $data): ToolCompanyProfile
    {
        return ToolCompanyProfile::query()->create([
            'user_id' => $userId,
            'name' => $data['name'],
            'legal_name' => $data['legal_name'] ?? null,
            'document' => $data['document'] ?? null,
            'office_name' => $data['office_name'] ?? null,
            'accountant_name' => $data['accountant_name'] ?? null,
            'accountant_registration' => $data['accountant_registration'] ?? null,
        ]);
    }

    /** @param array<string, mixed> $data */
    public function storeEmployee(int $userId, array $data): ToolEmployeeProfile
    {
        $companyId = isset($data['company_profile_id']) ? (int) $data['company_profile_id'] : null;
        if ($companyId !== null) {
            $this->findCompanyOwned($companyId, $userId);
        }

        return ToolEmployeeProfile::query()->create([
            'user_id' => $userId,
            'company_profile_id' => $companyId,
            'name' => $data['name'],
            'document' => $data['document'] ?? null,
            'department' => $data['department'] ?? null,
            'role' => $data['role'] ?? null,
            'defaults' => $data['defaults'] ?? [],
        ]);
    }

    public function findCompanyOwned(int $id, int $userId): ToolCompanyProfile
    {
        return ToolCompanyProfile::query()
            ->whereKey($id)
            ->where('user_id', $userId)
            ->firstOrFail();
    }

    public function findEmployeeOwned(int $id, int $userId): ToolEmployeeProfile
    {
        return ToolEmployeeProfile::query()
            ->whereKey($id)
            ->where('user_id', $userId)
            ->with('company')
            ->firstOrFail();
    }

    public function deleteCompanyOwned(int $id, int $userId): void
    {
        DB::transaction(function () use ($id, $userId): void {
            $this->findCompanyOwned($id, $userId)->delete();
        });
    }

    public function deleteEmployeeOwned(int $id, int $userId): void
    {
        DB::transaction(function () use ($id, $userId): void {
            $this->findEmployeeOwned($id, $userId)->delete();
        });
    }

    public function assertCompanyOwned(?int $id, int $userId): void
    {
        if ($id === null) {
            return;
        }

        if (! ToolCompanyProfile::query()->whereKey($id)->where('user_id', $userId)->exists()) {
            throw (new ModelNotFoundException)->setModel(ToolCompanyProfile::class, [$id]);
        }
    }
}
