<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Infrastructure\Repositories;

use App\Tools\AccountingFeesCalculator\Application\Data\AccountingClientInput;
use App\Tools\AccountingFeesCalculator\Application\Data\AccountingFeesOwner;
use App\Tools\AccountingFeesCalculator\Infrastructure\Models\AccountingClient;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final class AccountingClientRepository
{
    /** @return LengthAwarePaginator<AccountingClient> */
    public function paginate(
        AccountingFeesOwner $owner,
        string $search,
        string $status,
        int $perPage = 12,
    ): LengthAwarePaginator {
        return AccountingClient::query()
            ->visibleTo($owner->userId, $owner->sessionKey)
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($nested) use ($search): void {
                    $nested->where('company_name', 'like', "%{$search}%")
                        ->orWhere('document', 'like', "%{$search}%")
                        ->orWhere('contact_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(
                in_array($status, ['prospect', 'negotiation', 'client', 'inactive'], true),
                fn ($query) => $query->where('pipeline_status', $status),
            )
            ->latest('updated_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    /** @return Collection<string, int> */
    public function summary(AccountingFeesOwner $owner): Collection
    {
        return AccountingClient::query()
            ->visibleTo($owner->userId, $owner->sessionKey)
            ->selectRaw('pipeline_status, COUNT(*) as total')
            ->groupBy('pipeline_status')
            ->pluck('total', 'pipeline_status');
    }

    public function make(): AccountingClient
    {
        return new AccountingClient;
    }

    public function store(AccountingFeesOwner $owner, AccountingClientInput $input): AccountingClient
    {
        return AccountingClient::query()->create([
            ...$input->toPersistenceArray(),
            'user_id' => $owner->userId,
            'session_key' => $owner->userId === null ? $owner->sessionKey : null,
        ]);
    }

    public function update(AccountingClient $client, AccountingClientInput $input): void
    {
        $client->update($input->toPersistenceArray());
    }

    public function delete(AccountingClient $client): void
    {
        $client->delete();
    }
}
