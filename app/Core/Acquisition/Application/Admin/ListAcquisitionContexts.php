<?php

namespace App\Core\Acquisition\Application\Admin;

use App\Core\Acquisition\Contracts\AcquisitionContextAdministration;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class ListAcquisitionContexts
{
    public function __construct(private AcquisitionContextAdministration $contexts) {}

    /** @return LengthAwarePaginator<int, array<string, mixed>> */
    public function execute(?string $search, ?string $status): LengthAwarePaginator
    {
        return $this->contexts->paginate($search, $status);
    }
}
