<?php

namespace App\Core\Analytics\Infrastructure\Http;

use App\Core\Analytics\Domain\ValueObjects\Acquisition;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

final class AcquisitionResolver
{
    public function resolve(Request $request): Acquisition
    {
        $utm = collect(['source', 'medium', 'campaign', 'term', 'content'])
            ->mapWithKeys(fn (string $key): array => [$key => $this->clean($request->query("utm_{$key}"))])
            ->all();

        if ($utm['source'] !== null) {
            return new Acquisition(
                source: Str::lower($utm['source']),
                medium: Str::lower($utm['medium'] ?? 'campaign'),
                campaign: $utm['campaign'],
                utm: $utm,
                referrerHost: $this->referrerHost($request),
            );
        }

        $host = $this->referrerHost($request);
        if ($host === null || $this->isInternal($host, $request->getHost())) {
            return new Acquisition('direct', 'none', utm: $utm);
        }

        foreach ((array) config('analytics.acquisition.sources', []) as $source => $definition) {
            foreach ((array) ($definition['hosts'] ?? []) as $needle) {
                if ($this->matchesHost($host, (string) $needle)) {
                    return new Acquisition(
                        source: (string) $source,
                        medium: (string) ($definition['medium'] ?? 'referral'),
                        utm: $utm,
                        referrerHost: $host,
                    );
                }
            }
        }

        return new Acquisition($host, 'referral', utm: $utm, referrerHost: $host);
    }

    private function clean(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : Str::limit($value, 255, '');
    }

    private function referrerHost(Request $request): ?string
    {
        $host = Str::lower((string) parse_url((string) $request->headers->get('referer'), PHP_URL_HOST));

        return $host === '' ? null : preg_replace('/^www\./', '', $host);
    }

    private function isInternal(string $host, string $currentHost): bool
    {
        $currentHost = preg_replace('/^www\./', '', Str::lower($currentHost));

        return $host === $currentHost || str_ends_with($host, '.'.$currentHost);
    }

    private function matchesHost(string $host, string $needle): bool
    {
        $needle = preg_replace('/^www\./', '', Str::lower(trim($needle)));

        return $host === $needle || str_ends_with($host, '.'.$needle);
    }
}
