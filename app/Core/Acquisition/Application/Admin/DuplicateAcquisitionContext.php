<?php

namespace App\Core\Acquisition\Application\Admin;

use App\Core\Acquisition\Contracts\AcquisitionContextAdministration;

final readonly class DuplicateAcquisitionContext
{
    public function __construct(private AcquisitionContextAdministration $contexts) {}

    public function execute(int $id): int
    {
        return $this->contexts->duplicate($id);
    }
}
