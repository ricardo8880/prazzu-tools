<?php

namespace App\Core\Verticals\Application\Session;

use App\Core\Verticals\Contracts\VerticalRegistry;
use App\Core\Verticals\Domain\Data\Vertical;
use Illuminate\Contracts\Session\Session;

final readonly class VerticalContextSession
{
    public function __construct(
        private VerticalRegistry $verticals,
    ) {}

    public function activate(Session $session, Vertical $vertical): void
    {
        $session->put($this->sessionKey(), [
            'slug' => $vertical->slug,
            'activated_at' => now()->toIso8601String(),
        ]);
    }

    public function active(Session $session): ?Vertical
    {
        $slug = $session->get($this->sessionKey().'.slug');

        if (! is_string($slug)) {
            return null;
        }

        $vertical = $this->verticals->find($slug);

        if ($vertical === null) {
            $this->clear($session);
        }

        return $vertical;
    }

    public function clear(Session $session): void
    {
        $session->forget($this->sessionKey());
    }

    private function sessionKey(): string
    {
        return (string) config('verticals.session_key', 'vertical.context');
    }
}
