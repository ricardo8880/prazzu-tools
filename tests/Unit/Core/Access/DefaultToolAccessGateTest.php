<?php

namespace Tests\Unit\Core\Access;

use App\Core\Access\Contracts\CommercialAccessPolicy;
use App\Core\Access\Data\ToolAccessContext;
use App\Core\Access\Enums\AccountRole;
use App\Core\Access\Enums\SubscriptionPlan;
use App\Core\Access\Services\DefaultToolAccessGate;
use App\Core\FeatureFlags\Contracts\FeatureFlagRepository;
use App\Core\Tools\Data\ToolManifest;
use App\Core\Tools\Enums\ToolAccess;
use App\Core\Tools\Enums\ToolCategory;
use App\Core\Tools\Enums\ToolStatus;
use PHPUnit\Framework\TestCase;

final class DefaultToolAccessGateTest extends TestCase
{
    public function test_launch_free_mode_grants_public_capabilities_to_visitors(): void
    {
        $gate = new DefaultToolAccessGate($this->flags(true), $this->commercialPolicy(true));

        self::assertTrue($gate->decide($this->manifest(ToolAccess::Free), new ToolAccessContext())->allowed);
        self::assertTrue($gate->decide($this->manifest(ToolAccess::Authenticated), new ToolAccessContext())->allowed);
        self::assertTrue($gate->decide($this->manifest(ToolAccess::Premium), new ToolAccessContext())->allowed);
    }

    public function test_monetized_mode_requires_authenticated_premium_account_for_premium_tool(): void
    {
        $gate = new DefaultToolAccessGate($this->flags(true), $this->commercialPolicy(false));
        $manifest = $this->manifest(ToolAccess::Premium);

        self::assertFalse($gate->decide($manifest, new ToolAccessContext())->allowed);
        self::assertFalse($gate->decide($manifest, new ToolAccessContext(userId: 1))->allowed);
        self::assertTrue($gate->decide($manifest, new ToolAccessContext(userId: 1, plan: SubscriptionPlan::Premium))->allowed);
    }

    public function test_internal_tool_requires_administrator_even_during_launch_free_mode(): void
    {
        $gate = new DefaultToolAccessGate($this->flags(true), $this->commercialPolicy(true));
        $manifest = $this->manifest(ToolAccess::Internal);

        self::assertFalse($gate->decide($manifest, new ToolAccessContext())->allowed);
        self::assertTrue($gate->decide($manifest, new ToolAccessContext(userId: 1, role: AccountRole::Administrator))->allowed);
    }

    public function test_feature_flag_can_block_execution_during_launch_free_mode(): void
    {
        $gate = new DefaultToolAccessGate($this->flags(false), $this->commercialPolicy(true));

        self::assertSame('tool.feature_disabled', $gate->decide($this->manifest(ToolAccess::Free), new ToolAccessContext())->reason);
    }

    public function test_tool_status_can_block_execution_during_launch_free_mode(): void
    {
        $gate = new DefaultToolAccessGate($this->flags(true), $this->commercialPolicy(true));
        $manifest = new ToolManifest(
            slug: 'ferramenta-teste',
            name: 'Ferramenta Teste',
            description: 'Descrição da ferramenta de teste.',
            category: ToolCategory::Calculators,
            icon: 'bi-calculator',
            routeName: 'tools.ferramenta-teste.index',
            access: ToolAccess::Premium,
            status: ToolStatus::Maintenance,
        );

        self::assertSame('tool.status_blocks_execution', $gate->decide($manifest, new ToolAccessContext())->reason);
    }

    private function manifest(ToolAccess $access): ToolManifest
    {
        return new ToolManifest(
            slug: 'ferramenta-teste',
            name: 'Ferramenta Teste',
            description: 'Descrição da ferramenta de teste.',
            category: ToolCategory::Calculators,
            icon: 'bi-calculator',
            routeName: 'tools.ferramenta-teste.index',
            access: $access,
            status: ToolStatus::Active,
        );
    }

    private function flags(bool $enabled): FeatureFlagRepository
    {
        return new class($enabled) implements FeatureFlagRepository {
            public function __construct(private readonly bool $enabled) {}
            public function enabled(string $flag, bool $default = false): bool { return $this->enabled; }
        };
    }

    private function commercialPolicy(bool $launchFree): CommercialAccessPolicy
    {
        return new class($launchFree) implements CommercialAccessPolicy {
            public function __construct(private readonly bool $launchFree) {}
            public function grantsPublicCapabilitiesWithoutAuthentication(): bool { return $this->launchFree; }
            public function enforcesUsageLimits(): bool { return ! $this->launchFree; }
        };
    }
}
