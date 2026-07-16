<?php

namespace Tests\Unit\Core\Access;

use App\Core\Access\Contracts\CommercialAccessPolicy;
use App\Core\Access\Contracts\ToolAccessGate;
use App\Core\Access\Data\AccessDecision;
use App\Core\Access\Data\ToolAccessContext;
use App\Core\Access\Services\ToolExecutionAuthorizer;
use App\Core\Tools\Data\ToolManifest;
use App\Core\Tools\Enums\ToolCategory;
use App\Core\Usage\Contracts\UsageLimiter;
use App\Core\Usage\Data\UsageDecision;
use App\Core\Usage\Data\UsageLimit;
use PHPUnit\Framework\TestCase;

final class ToolExecutionAuthorizerTest extends TestCase
{
    public function test_launch_free_mode_does_not_consume_commercial_usage_limit(): void
    {
        $limiter = new class implements UsageLimiter {
            public bool $consumed = false;

            public function consume(string $toolSlug, string $subjectKey, UsageLimit $limit): UsageDecision
            {
                $this->consumed = true;

                return new UsageDecision(false, 0, 60);
            }
        };

        $authorizer = new ToolExecutionAuthorizer(
            $this->allowingGate(),
            $limiter,
            $this->commercialPolicy(false),
        );

        $decision = $authorizer->authorize(
            $this->manifest(),
            new ToolAccessContext(),
            'ip:127.0.0.1',
            new UsageLimit(1, 3600),
        );

        self::assertTrue($decision->allowed);
        self::assertFalse($limiter->consumed);
        self::assertSame('tool.launch_free_execution_allowed', $decision->reason);
    }

    public function test_monetized_mode_applies_commercial_usage_limit(): void
    {
        $limiter = new class implements UsageLimiter {
            public function consume(string $toolSlug, string $subjectKey, UsageLimit $limit): UsageDecision
            {
                return new UsageDecision(false, 0, 60);
            }
        };

        $authorizer = new ToolExecutionAuthorizer(
            $this->allowingGate(),
            $limiter,
            $this->commercialPolicy(true),
        );

        $decision = $authorizer->authorize(
            $this->manifest(),
            new ToolAccessContext(),
            'ip:127.0.0.1',
            new UsageLimit(1, 3600),
        );

        self::assertFalse($decision->allowed);
        self::assertSame('tool.usage_limit_reached', $decision->reason);
    }

    private function allowingGate(): ToolAccessGate
    {
        return new class implements ToolAccessGate {
            public function decide(ToolManifest $manifest, ToolAccessContext $context): AccessDecision
            {
                return AccessDecision::allow();
            }
        };
    }

    private function commercialPolicy(bool $enforcesUsageLimits): CommercialAccessPolicy
    {
        return new class($enforcesUsageLimits) implements CommercialAccessPolicy {
            public function __construct(private readonly bool $enforcesUsageLimits) {}
            public function grantsPublicCapabilitiesWithoutAuthentication(): bool { return ! $this->enforcesUsageLimits; }
            public function enforcesUsageLimits(): bool { return $this->enforcesUsageLimits; }
        };
    }

    private function manifest(): ToolManifest
    {
        return new ToolManifest(
            slug: 'ferramenta-teste',
            name: 'Ferramenta Teste',
            description: 'Descrição da ferramenta de teste.',
            category: ToolCategory::Calculators,
            icon: 'bi-calculator',
            routeName: 'tools.ferramenta-teste.index',
        );
    }
}
