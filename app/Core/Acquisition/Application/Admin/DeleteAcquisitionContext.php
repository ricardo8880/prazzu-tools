<?php

namespace App\Core\Acquisition\Application\Admin;

use App\Core\Acquisition\Contracts\AcquisitionContextAdministration;

final readonly class DeleteAcquisitionContext
{
    public function __construct(private AcquisitionContextAdministration $contexts) {}

    public function execute(int $id): void
    {
        $this->contexts->delete($id);
    }
}
