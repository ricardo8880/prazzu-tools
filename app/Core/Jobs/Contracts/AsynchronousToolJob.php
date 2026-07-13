<?php

namespace App\Core\Jobs\Contracts;

interface AsynchronousToolJob
{
    public function toolSlug(): string;
}
