<?php

namespace App\Http\Controllers\Platform;

use App\Core\Analytics\Contracts\PlatformAnalytics;
use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use App\Core\Newsletter\Models\NewsletterSubscriber;
use App\Core\Verticals\Application\VerticalContext;
use App\Http\Controllers\Controller;
use App\Http\Requests\Platform\NewsletterRequest;
use Illuminate\Http\RedirectResponse;

final class NewsletterController extends Controller
{
    public function store(
        NewsletterRequest $request,
        VerticalContext $verticalContext,
        PlatformAnalytics $analytics,
    ): RedirectResponse {
        $email = strtolower(trim((string) $request->validated('email')));
        $sourcePath = parse_url(url()->previous(), PHP_URL_PATH);
        $normalizedSourcePath = is_string($sourcePath) && $sourcePath !== '' ? $sourcePath : '/';
        $authenticatedUser = $request->user();
        $authenticatedEmail = strtolower(trim((string) ($authenticatedUser?->email ?? '')));
        $matchingUserId = $authenticatedUser !== null && hash_equals($authenticatedEmail, $email)
            ? $authenticatedUser->getAuthIdentifier()
            : null;

        $subscriber = NewsletterSubscriber::query()->firstOrCreate(
            ['email' => $email],
            [
                'user_id' => $matchingUserId,
                'vertical_slug' => $verticalContext->slug(),
                'source_path' => $normalizedSourcePath,
                'subscribed_at' => now(),
                'unsubscribed_at' => null,
            ],
        );

        $isReactivation = ! $subscriber->wasRecentlyCreated && $subscriber->unsubscribed_at !== null;
        $isNewSubscription = $subscriber->wasRecentlyCreated || $isReactivation;

        if (! $subscriber->wasRecentlyCreated) {
            $subscriber->fill([
                'user_id' => $subscriber->user_id ?? $matchingUserId,
                'vertical_slug' => $verticalContext->slug(),
                'source_path' => $normalizedSourcePath,
                'subscribed_at' => $isReactivation ? now() : $subscriber->subscribed_at,
                'unsubscribed_at' => null,
            ])->save();
        }

        if ($isNewSubscription) {
            $analytics->record(
                AnalyticsEventName::NewsletterSubscribed->value,
                'engagement',
                $request,
                array_filter([
                    'vertical' => $verticalContext->slug(),
                    'source_path' => $subscriber->source_path,
                ], static fn (mixed $value): bool => $value !== null && $value !== ''),
            );
        }

        return back()->with(
            'status',
            'Inscrição confirmada. Você receberá avisos sobre novas ferramentas e atualizações relevantes da plataforma.',
        );
    }
}
