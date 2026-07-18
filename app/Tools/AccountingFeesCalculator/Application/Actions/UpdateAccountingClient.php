<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Application\Actions;

use App\Tools\AccountingFeesCalculator\Application\Access\AccountingFeesRecordAccess;
use App\Tools\AccountingFeesCalculator\Application\Data\AccountingClientInput;
use App\Tools\AccountingFeesCalculator\Application\Data\AccountingFeesOwner;
use App\Tools\AccountingFeesCalculator\Infrastructure\Models\AccountingClient;
use App\Tools\AccountingFeesCalculator\Infrastructure\Repositories\AccountingClientRepository;

final readonly class UpdateAccountingClient
{
    public function __construct(
        private AccountingFeesRecordAccess $access,
        private AccountingClientRepository $clients,
    ) {}

    /** @param array<string, mixed> $input */
    public function execute(AccountingClient $client, array $input, AccountingFeesOwner $owner): void
    {
        $this->access->ensureOwnedBy($owner, $client->user_id, $client->session_key);
        $this->clients->update($client, AccountingClientInput::fromArray($input));
    }
}
