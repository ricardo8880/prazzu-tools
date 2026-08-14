<?php

declare(strict_types=1);

namespace App\Tools\DigitalCertificateAnalyzer\Tests\Feature;

use App\Core\Access\Contracts\CommercialAccessPolicy;
use App\Core\Access\Contracts\ToolAccessContextResolver;
use App\Core\Access\Data\ToolAccessContext;
use App\Core\Access\Enums\SubscriptionPlan;
use App\Core\Access\Services\DefaultToolAccessGate;
use App\Core\Access\Services\DefaultToolFeatureAccessGate;
use App\Core\FeatureFlags\Contracts\FeatureFlagRepository;
use App\Tools\DigitalCertificateAnalyzer\Tool;
use Illuminate\Contracts\Auth\Authenticatable;
use PHPUnit\Framework\TestCase;

final class PlusFeaturesTest extends TestCase
{
    public function test_technical_report_blocks_free_and_allows_plus_in_monetized_mode(): void
    {
        $manifest = (new Tool)->manifest();
        $user = $this->createStub(Authenticatable::class);
        $free = $this->gate(SubscriptionPlan::Free)->decide($manifest, 'technical_report', $user);
        $plus = $this->gate(SubscriptionPlan::Plus)->decide($manifest, 'technical_report', $user);
        self::assertFalse($free->allowed);
        self::assertSame('feature.plus_required', $free->reason);
        self::assertTrue($plus->allowed);
        self::assertSame('feature.plus_plan', $plus->reason);
    }

    private function gate(SubscriptionPlan $plan): DefaultToolFeatureAccessGate
    {
        $flags = new class implements FeatureFlagRepository { public function enabled(string $flag, bool $default = false): bool { return true; } };
        $policy = new class implements CommercialAccessPolicy { public function grantsPublicCapabilitiesWithoutAuthentication(): bool { return false; } };
        $resolver = new class($plan) implements ToolAccessContextResolver {
            public function __construct(private readonly SubscriptionPlan $plan) {}
            public function resolve(?Authenticatable $user): ToolAccessContext { return new ToolAccessContext(userId: 1, plan: $this->plan); }
        };
        return new DefaultToolFeatureAccessGate(new DefaultToolAccessGate($flags, $policy), $resolver, $policy, $flags);
    }
}
