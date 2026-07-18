<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Application\Actions;

use App\Tools\AccountingFeesCalculator\Application\Data\AccountingFeesOwner;
use App\Tools\AccountingFeesCalculator\Infrastructure\Repositories\AccountingClientRepository;

final readonly class ListAccountingClients
{
    public function __construct(private AccountingClientRepository $clients) {}

    /** @return array{clients: mixed, summary: mixed} */
    public function execute(AccountingFeesOwner $owner, string $search, string $status): array
    {
        return [
            'clients' => $this->clients->paginate($owner, $search, $status),
            'summary' => $this->clients->summary($owner),
        ];
    }
}
