<?php

namespace App\Core\Acquisition\Application\Admin;

use App\Core\Acquisition\Contracts\AcquisitionContextAdministration;

final readonly class SaveAcquisitionContext
{
    public function __construct(private AcquisitionContextAdministration $contexts) {}

    /** @param array<string, mixed> $data */
    public function execute(?int $id, array $data): int
    {
        return $this->contexts->save($id, $data);
    }
}
