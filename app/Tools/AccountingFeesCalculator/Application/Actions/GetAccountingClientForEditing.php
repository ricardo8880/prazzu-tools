<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Application\Actions;

use App\Tools\AccountingFeesCalculator\Application\Access\AccountingFeesRecordAccess;
use App\Tools\AccountingFeesCalculator\Application\Data\AccountingFeesOwner;
use App\Tools\AccountingFeesCalculator\Infrastructure\Models\AccountingClient;

final readonly class GetAccountingClientForEditing
{
    public function __construct(private AccountingFeesRecordAccess $access) {}

    public function execute(AccountingClient $client, AccountingFeesOwner $owner): AccountingClient
    {
        $this->access->ensureOwnedBy($owner, $client->user_id, $client->session_key);

        return $client;
    }
}
