<?php

namespace App\Core\Acquisition\Application\Admin;

use App\Core\Acquisition\Contracts\AcquisitionContextAdministration;

final readonly class ToggleAcquisitionContext
{
    public function __construct(private AcquisitionContextAdministration $contexts) {}

    public function execute(int $id): bool
    {
        return $this->contexts->toggle($id);
    }
}
