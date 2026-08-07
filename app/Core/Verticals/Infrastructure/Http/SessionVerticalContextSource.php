<?php

namespace App\Core\Verticals\Infrastructure\Http;

use App\Core\Verticals\Application\Session\VerticalContextSession;
use App\Core\Verticals\Contracts\VerticalContextSource;
use App\Core\Verticals\Domain\Data\Vertical;
use Illuminate\Http\Request;

final readonly class SessionVerticalContextSource implements VerticalContextSource
{
    public function __construct(
        private VerticalContextSession $contexts,
    ) {}

    public function resolve(Request $request): ?Vertical
    {
        return $request->hasSession()
            ? $this->contexts->active($request->session())
            : null;
    }
}
