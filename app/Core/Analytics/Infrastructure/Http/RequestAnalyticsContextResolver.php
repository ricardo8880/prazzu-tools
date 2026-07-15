<?php

namespace App\Core\Analytics\Infrastructure\Http;

use App\Core\Analytics\Contracts\AnalyticsContextResolver;
use App\Core\Analytics\Domain\ValueObjects\Acquisition;
use App\Core\Analytics\Domain\ValueObjects\AnalyticsContext;
use App\Core\Analytics\Models\AnalyticsSession;
use App\Core\Analytics\Models\AnalyticsVisitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class RequestAnalyticsContextResolver implements AnalyticsContextResolver
{
    public function __construct(
        private readonly UserAgentParser $userAgentParser,
        private readonly AcquisitionResolver $acquisitionResolver,
    ) {}

    public function resolve(?Request $request = null): AnalyticsContext
    {
        $request ??= request();
        if (! $request instanceof Request) {
            return new AnalyticsContext();
        }

        $visitorId = $this->visitorId($request);
        $acquisition = $this->acquisitionResolver->resolve($request);
        $agent = $this->userAgentParser->parse($request->userAgent());
        $userId = $request->user()?->getAuthIdentifier();
        $now = now();

        $sessionId = DB::transaction(function () use (
            $request,
            $visitorId,
            $userId,
            $acquisition,
            $agent,
            $now,
        ): string {
            $this->persistVisitor(
                request: $request,
                visitorId: $visitorId,
                userId: $userId,
                acquisition: $acquisition,
                now: $now,
            );

            return $this->resolveAnalyticsSessionId(
                request: $request,
                visitorId: $visitorId,
                userId: $userId,
                acquisition: $acquisition,
                agent: $agent,
                now: $now,
            );
        }, 3);

        $utm = $acquisition->utm;

        return new AnalyticsContext(
            visitorId: $visitorId,
            analyticsSessionId: $sessionId,
            userId: $userId,
            laravelSessionId: $request->hasSession() ? $request->session()->getId() : null,
            url: $request->fullUrl(),
            path: '/'.$request->path(),
            referrer: $request->headers->get('referer'),
            source: $acquisition->source,
            medium: $acquisition->medium,
            campaign: $acquisition->campaign,
            utm: $utm,
            deviceType: $agent['device_type'],
            browser: $agent['browser'],
            operatingSystem: $agent['operating_system'],
            language: $request->header('X-Analytics-Language') ?: $request->getPreferredLanguage(),
            timezone: $request->header('X-Timezone'),
            screenResolution: $request->header('X-Screen-Resolution'),
            countryCode: $request->header('CF-IPCountry'),
            region: $request->header('X-Region'),
            city: $request->header('X-City'),
            ipHash: $request->ip() ? hash_hmac('sha256', $request->ip(), (string) config('app.key')) : null,
            userAgent: Str::limit((string) $request->userAgent(), 500, ''),
        );
    }

    private function persistVisitor(
        Request $request,
        string $visitorId,
        int|string|null $userId,
        Acquisition $acquisition,
        mixed $now,
    ): void {
        /** @var AnalyticsVisitor $visitor */
        $visitor = AnalyticsVisitor::query()->firstOrNew(['id' => $visitorId]);

        if (! $visitor->exists) {
            $visitor->first_seen_at = $now;
            $visitor->first_source = $acquisition->source;
            $visitor->first_medium = $acquisition->medium;
            $visitor->first_campaign = $acquisition->campaign;
            $visitor->first_utm = $acquisition->utm;
            $visitor->first_referrer = $request->headers->get('referer');
        }

        if ($userId !== null) {
            $visitor->user_id = $userId;
        }

        $visitor->last_seen_at = $now;
        $visitor->last_source = $acquisition->source;
        $visitor->last_medium = $acquisition->medium;
        $visitor->last_campaign = $acquisition->campaign;
        $visitor->last_utm = $acquisition->utm;
        $visitor->last_referrer = $request->headers->get('referer');
        $visitor->save();
    }

    private function visitorId(Request $request): string
    {
        $attribute = $request->attributes->get('analytics.visitor_id');
        if (is_string($attribute) && Str::isUuid($attribute)) {
            return $attribute;
        }

        $cookie = $request->cookie((string) config('analytics.visitor_cookie'));
        if (is_string($cookie) && Str::isUuid($cookie)) {
            return $cookie;
        }

        return (string) Str::uuid();
    }

    /** @param array{device_type:string,browser:string,operating_system:string} $agent */
    private function resolveAnalyticsSessionId(
        Request $request,
        string $visitorId,
        int|string|null $userId,
        Acquisition $acquisition,
        array $agent,
        mixed $now,
    ): string {
        $attribute = $request->attributes->get('analytics.session_id');
        if (is_string($attribute) && Str::isUuid($attribute)) {
            $existingSession = AnalyticsSession::query()
                ->whereKey($attribute)
                ->where('visitor_id', $visitorId)
                ->first();

            if ($existingSession !== null) {
                $this->touchSession($existingSession, $request, $userId, $now);

                return $attribute;
            }
        }

        $cutoff = now()->subMinutes((int) config('analytics.session_timeout_minutes', 30));
        $session = AnalyticsSession::query()
            ->where('visitor_id', $visitorId)
            ->where('last_activity_at', '>=', $cutoff)
            ->latest('last_activity_at')
            ->first();

        if ($session !== null) {
            $this->touchSession($session, $request, $userId, $now);

            return (string) $session->getKey();
        }

        $id = (string) Str::uuid();
        AnalyticsSession::query()->create([
            'id' => $id,
            'visitor_id' => $visitorId,
            'user_id' => $userId,
            'started_at' => $now,
            'last_activity_at' => $now,
            'landing_url' => $request->fullUrl(),
            'landing_path' => '/'.$request->path(),
            'referrer' => $request->headers->get('referer'),
            'source' => $acquisition->source,
            'medium' => $acquisition->medium,
            'campaign' => $acquisition->campaign,
            'utm_source' => $acquisition->utm['source'] ?? null,
            'utm_medium' => $acquisition->utm['medium'] ?? null,
            'utm_campaign' => $acquisition->utm['campaign'] ?? null,
            'utm_term' => $acquisition->utm['term'] ?? null,
            'utm_content' => $acquisition->utm['content'] ?? null,
            'device_type' => $agent['device_type'],
            'browser' => $agent['browser'],
            'operating_system' => $agent['operating_system'],
            'language' => $request->header('X-Analytics-Language') ?: $request->getPreferredLanguage(),
            'timezone' => $request->header('X-Timezone'),
            'screen_resolution' => $request->header('X-Screen-Resolution'),
            'country_code' => $request->header('CF-IPCountry'),
            'region' => $request->header('X-Region'),
            'city' => $request->header('X-City'),
        ]);

        return $id;
    }

    private function touchSession(
        AnalyticsSession $session,
        Request $request,
        int|string|null $userId,
        mixed $now,
    ): void {
        if ($userId !== null) {
            $session->user_id = $userId;
        }

        $session->last_activity_at = $now;
        $session->language = $request->header('X-Analytics-Language') ?: $request->getPreferredLanguage();

        if ($request->headers->has('X-Timezone')) {
            $session->timezone = $request->header('X-Timezone');
        }

        if ($request->headers->has('X-Screen-Resolution')) {
            $session->screen_resolution = $request->header('X-Screen-Resolution');
        }

        $session->save();
    }
}
