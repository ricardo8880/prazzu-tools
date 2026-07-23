<?php

declare(strict_types=1);

namespace App\Tools\ReceiptIssuer\Application\Actions;

use App\Tools\ReceiptIssuer\Infrastructure\Models\ReceiptPartyProfile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class ManageReceiptPartyProfiles
{
    /** @return Collection<int, ReceiptPartyProfile> */
    public function all(int $userId): Collection
    {
        return ReceiptPartyProfile::query()
            ->where('user_id', $userId)
            ->orderBy('party_type')
            ->orderBy('label')
            ->get();
    }

    /** @param array<string, mixed> $data */
    public function save(int $userId, array $data): ReceiptPartyProfile
    {
        return ReceiptPartyProfile::query()->updateOrCreate(
            ['user_id' => $userId, 'party_type' => $data['party_type'], 'label' => $data['label']],
            [
                'name' => $data['name'],
                'document_type' => $data['document_type'] ?: null,
                'document' => $data['document'] ?: null,
            ],
        );
    }

    public function owned(int $profileId, int $userId): ReceiptPartyProfile
    {
        $profile = ReceiptPartyProfile::query()->whereKey($profileId)->where('user_id', $userId)->first();

        if (! $profile) {
            throw (new ModelNotFoundException)->setModel(ReceiptPartyProfile::class, [$profileId]);
        }

        return $profile;
    }

    public function delete(int $profileId, int $userId): void
    {
        $this->owned($profileId, $userId)->delete();
    }
}
