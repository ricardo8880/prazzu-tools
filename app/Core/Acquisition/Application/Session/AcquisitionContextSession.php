<?php

namespace App\Core\Acquisition\Application\Session;

use App\Core\Acquisition\Application\ResolveAcquisitionContext;
use App\Core\Acquisition\Domain\Data\AcquisitionContext;
use Illuminate\Contracts\Session\Session;

final readonly class AcquisitionContextSession
{
    public const MODE_CONTEXTUAL = 'contextual';

    public const MODE_FREE = 'free';

    public function __construct(
        private ResolveAcquisitionContext $contexts,
    ) {}

    public function activate(Session $session, AcquisitionContext $context): void
    {
        $session->put($this->sessionKey(), [
            'keyword' => $context->keyword,
            'activated_at' => now()->toIso8601String(),
            'mode' => self::MODE_CONTEXTUAL,
        ]);
    }

    public function active(Session $session): ?AcquisitionContext
    {
        $keyword = $session->get($this->sessionKey().'.keyword');

        if (! is_string($keyword)) {
            return null;
        }

        $context = $this->contexts->execute($keyword);

        if ($context === null) {
            $this->clear($session);
        }

        return $context;
    }

    public function mode(Session $session): string
    {
        $mode = $session->get($this->sessionKey().'.mode');

        return $mode === self::MODE_FREE
            ? self::MODE_FREE
            : self::MODE_CONTEXTUAL;
    }

    public function exploreFreely(Session $session): void
    {
        $session->put($this->sessionKey().'.mode', self::MODE_FREE);
    }

    public function continueContext(Session $session): void
    {
        $session->put($this->sessionKey().'.mode', self::MODE_CONTEXTUAL);
    }

    public function clear(Session $session): void
    {
        $session->forget($this->sessionKey());
    }

    private function sessionKey(): string
    {
        return (string) config('acquisition.session_key', 'acquisition.context');
    }
}
