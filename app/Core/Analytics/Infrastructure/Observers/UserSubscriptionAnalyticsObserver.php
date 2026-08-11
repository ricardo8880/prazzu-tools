<?php

namespace App\Core\Analytics\Infrastructure\Observers;

use App\Core\Access\Enums\SubscriptionPlan;
use App\Core\Analytics\Contracts\PlatformAnalytics;
use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use App\Core\Analytics\Domain\Events\AnalyticsEvent;
use App\Models\User;
use Illuminate\Http\Request;

final readonly class UserSubscriptionAnalyticsObserver
{
    public function __construct(private PlatformAnalytics $analytics) {}

    public function updated(User $user): void
    {
        if (! $user->wasChanged('subscription_plan') || $user->subscription_plan !== SubscriptionPlan::Plus) {
            return;
        }

        $request = app()->bound('request') ? app('request') : null;
        if (! $request instanceof Request) {
            return;
        }

        $this->analytics->track(new AnalyticsEvent(
            name: AnalyticsEventName::SubscriptionCreated->value,
            channel: 'subscription',
            properties: [
                'previous_plan' => $this->originalPlan($user),
                'plan' => SubscriptionPlan::Plus->value,
            ],
            subjectType: 'account',
            subjectId: $user->getKey(),
        ), $request);
    }

    private function originalPlan(User $user): string
    {
        $value = $user->getRawOriginal('subscription_plan');

        return $value instanceof SubscriptionPlan ? $value->value : (string) ($value ?: SubscriptionPlan::Free->value);
    }
}
