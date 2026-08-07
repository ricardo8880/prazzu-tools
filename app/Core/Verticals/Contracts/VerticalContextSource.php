<?php

namespace App\Core\Verticals\Contracts;

use App\Core\Verticals\Domain\Data\Vertical;
use Illuminate\Http\Request;

interface VerticalContextSource
{
    public function resolve(Request $request): ?Vertical;
}
