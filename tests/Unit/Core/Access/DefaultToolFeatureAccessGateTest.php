<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Access;

use App\Core\Access\Contracts\CommercialAccessPolicy;
use App\Core\Access\Contracts\ToolAccessContextResolver;
use App\Core\Access\Data\ToolAccessContext;
use App\Core\Access\Enums\SubscriptionPlan;
use App\Core\Access\Services\DefaultToolAccessGate;
use App\Core\Access\Services\DefaultToolFeatureAccessGate;
use App\Core\FeatureFlags\Contracts\FeatureFlagRepository;
use App\Core\Tools\Data\ToolFeature;
use App\Core\Tools\Data\ToolManifest;
use App\Core\Tools\Enums\ToolCategory;
use App\Core\Tools\Enums\ToolFeatureTier;
use Illuminate\Contracts\Auth\Authenticatable;
use PHPUnit\Framework\TestCase;

final class DefaultToolFeatureAccessGateTest extends TestCase
{
    public function test_essential_feature_remains_complete_for_a_visitor_in_monetized_mode(): void
    {
        $decision = $this->gate(new ToolAccessContext)->decide($this->manifest(), 'calculate');

        self::assertTrue($decision->allowed);
        self::assertSame('feature.essential', $decision->reason);
    }

    public function test_plus_feature_requires_authentication_and_an_effective_plus_plan_in_monetized_mode(): void
    {
        $manifest = $this->manifest();

        self::assertSame(
            'feature.authentication_required',
            $this->gate(new ToolAccessContext)->decide($manifest, 'scenarios')->reason,
        );
        self::assertSame(
            'feature.plus_required',
            $this->gate(new ToolAccessContext(userId: 10))->decide($manifest, 'scenarios', $this->user())->reason,
        );
        self::assertTrue(
            $this->gate(new ToolAccessContext(userId: 10, plan: SubscriptionPlan::Plus))
                ->decide($manifest, 'scenarios', $this->user())
                ->allowed,
        );
    }

    public function test_launch_policy_can_temporarily_release_plus_without_changing_the_manifest(): void
    {
        $decision = $this->gate(new ToolAccessContext, launchFree: true)
            ->decide($this->manifest(), 'scenarios');

        self::assertTrue($decision->allowed);
        self::assertSame('feature.launch_free', $decision->reason);
    }

    public function test_undeclared_feature_is_always_rejected(): void
    {
        $decision = $this->gate(new ToolAccessContext, launchFree: true)
            ->decide($this->manifest(), 'not_declared');

        self::assertFalse($decision->allowed);
        self::assertSame('feature.not_declared', $decision->reason);
    }

    public function test_feature_flag_can_disable_one_declared_feature(): void
    {
        $decision = $this->gate(new ToolAccessContext, launchFree: true, featureEnabled: false)
            ->decide($this->manifest(), 'scenarios');

        self::assertFalse($decision->allowed);
        self::assertSame('feature.disabled', $decision->reason);
    }

    private function gate(
        ToolAccessContext $context,
        bool $launchFree = false,
        bool $featureEnabled = true,
    ): DefaultToolFeatureAccessGate
    {
        $flags = new class($featureEnabled) implements FeatureFlagRepository
        {
            public function __construct(private readonly bool $featureEnabled) {}

            public function enabled(string $flag, bool $default = false): bool
            {
                if (str_contains($flag, '.features.')) {
                    return $this->featureEnabled;
                }

                return $default;
            }
        };
        $policy = new class($launchFree) implements CommercialAccessPolicy
        {
            public function __construct(private readonly bool $launchFree) {}

            public function grantsPublicCapabilitiesWithoutAuthentication(): bool
            {
                return $this->launchFree;
            }

        };
        $resolver = new class($context) implements ToolAccessContextResolver
        {
            public function __construct(private readonly ToolAccessContext $context) {}

            public function resolve(?Authenticatable $user): ToolAccessContext
            {
                return $this->context;
            }
        };

        return new DefaultToolFeatureAccessGate(
            new DefaultToolAccessGate($flags, $policy),
            $resolver,
            $policy,
            $flags,
        );
    }

    private function manifest(): ToolManifest
    {
        return new ToolManifest(
            slug: 'ferramenta-teste',
            name: 'Ferramenta Teste',
            description: 'Ferramenta usada para validar o acesso por recurso.',
            category: ToolCategory::Calculators,
            icon: 'bi-calculator',
            routeName: 'tools.ferramenta-teste.index',
            features: [
                new ToolFeature('calculate', 'Cálculo completo', ToolFeatureTier::Essential),
                new ToolFeature('scenarios', 'Cenários avançados', ToolFeatureTier::Plus),
            ],
        );
    }

    private function user(): Authenticatable
    {
        return $this->createStub(Authenticatable::class);
    }
}
