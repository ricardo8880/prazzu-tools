<?php

namespace App\Core\Tools\Contracts;

use Illuminate\Support\ServiceProvider;

interface HasServiceProviders
{
    /** @return array<int, class-string<ServiceProvider>> */
    public function serviceProviders(): array;
}
