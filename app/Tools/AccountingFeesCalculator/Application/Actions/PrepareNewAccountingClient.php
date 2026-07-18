<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Application\Actions;

use App\Tools\AccountingFeesCalculator\Infrastructure\Models\AccountingClient;
use App\Tools\AccountingFeesCalculator\Infrastructure\Repositories\AccountingClientRepository;

final readonly class PrepareNewAccountingClient
{
    public function __construct(private AccountingClientRepository $clients) {}

    public function execute(): AccountingClient
    {
        return $this->clients->make();
    }
}
