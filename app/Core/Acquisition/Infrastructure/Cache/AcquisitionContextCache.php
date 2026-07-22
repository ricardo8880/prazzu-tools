<?php

namespace App\Core\Acquisition\Infrastructure\Cache;

use App\Core\Acquisition\Domain\Data\AcquisitionContext;
use Closure;
use Illuminate\Contracts\Cache\Repository;

final readonly class AcquisitionContextCache
{
    private const PREFIX = 'acquisition:context:';

    public function __construct(private Repository $cache) {}

    /** @param Closure(): ?AcquisitionContext $resolver */
    public function remember(string $keyword, Closure $resolver): ?AcquisitionContext
    {
        $key = $this->key($keyword);
        $cached = $this->cache->get($key);

        if (is_array($cached) && array_key_exists('value', $cached)) {
            return $cached['value'] instanceof AcquisitionContext ? $cached['value'] : null;
        }

        $context = $resolver();

        $this->cache->put($key, ['value' => $context], $this->ttlSeconds());

        return $context;
    }

    public function forget(string $keyword): void
    {
        $keyword = trim($keyword);

        if ($keyword !== '') {
            $this->cache->forget($this->key($keyword));
        }
    }

    private function ttlSeconds(): int
    {
        return max(1, (int) config('acquisition.cache_ttl', 3600));
    }

    private function key(string $keyword): string
    {
        return self::PREFIX.hash('sha256', $keyword);
    }
}
