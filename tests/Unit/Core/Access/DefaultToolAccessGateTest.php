<?php

namespace Tests\Unit\Core\Access;

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
    public function test_premium_tool_requires_authenticated_premium_account(): void
    {
        $gate = new DefaultToolAccessGate($this->flags(true));
        $manifest = $this->manifest(ToolAccess::Premium);

        self::assertFalse($gate->decide($manifest, new ToolAccessContext())->allowed);
        self::assertFalse($gate->decide($manifest, new ToolAccessContext(userId: 1))->allowed);
        self::assertTrue($gate->decide($manifest, new ToolAccessContext(userId: 1, plan: SubscriptionPlan::Premium))->allowed);
    }

    public function test_internal_tool_requires_administrator(): void
    {
        $gate = new DefaultToolAccessGate($this->flags(true));
        $manifest = $this->manifest(ToolAccess::Internal);

        self::assertFalse($gate->decide($manifest, new ToolAccessContext(userId: 1))->allowed);
        self::assertTrue($gate->decide($manifest, new ToolAccessContext(userId: 1, role: AccountRole::Administrator))->allowed);
    }

    public function test_feature_flag_can_block_execution(): void
    {
        $gate = new DefaultToolAccessGate($this->flags(false));

        self::assertSame('tool.feature_disabled', $gate->decide($this->manifest(ToolAccess::Free), new ToolAccessContext())->reason);
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
}
