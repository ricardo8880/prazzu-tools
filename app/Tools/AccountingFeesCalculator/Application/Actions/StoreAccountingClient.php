<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Application\Actions;

use App\Tools\AccountingFeesCalculator\Application\Data\AccountingClientInput;
use App\Tools\AccountingFeesCalculator\Application\Data\AccountingFeesOwner;
use App\Tools\AccountingFeesCalculator\Infrastructure\Repositories\AccountingClientRepository;

final readonly class StoreAccountingClient
{
    public function __construct(private AccountingClientRepository $clients) {}

    /** @param array<string, mixed> $input */
    public function execute(array $input, AccountingFeesOwner $owner): void
    {
        $this->clients->store($owner, AccountingClientInput::fromArray($input));
    }
}
