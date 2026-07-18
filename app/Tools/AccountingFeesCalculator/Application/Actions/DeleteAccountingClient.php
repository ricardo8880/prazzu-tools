<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Application\Actions;

use App\Tools\AccountingFeesCalculator\Application\Access\AccountingFeesRecordAccess;
use App\Tools\AccountingFeesCalculator\Application\Data\AccountingFeesOwner;
use App\Tools\AccountingFeesCalculator\Infrastructure\Models\AccountingClient;
use App\Tools\AccountingFeesCalculator\Infrastructure\Repositories\AccountingClientRepository;

final readonly class DeleteAccountingClient
{
    public function __construct(
        private AccountingFeesRecordAccess $access,
        private AccountingClientRepository $clients,
    ) {}

    public function execute(AccountingClient $client, AccountingFeesOwner $owner): void
    {
        $this->access->ensureOwnedBy($owner, $client->user_id, $client->session_key);
        $this->clients->delete($client);
    }
}
