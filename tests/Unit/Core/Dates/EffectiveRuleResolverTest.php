<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Dates;

use App\Core\Dates\Contracts\EffectiveDated;
use App\Core\Dates\EffectivePeriod;
use App\Core\Dates\EffectiveRuleResolver;
use App\Core\Dates\Exceptions\NoEffectiveRule;
use App\Core\Dates\ReferenceDate;
use PHPUnit\Framework\TestCase;

final class EffectiveRuleResolverTest extends TestCase
{
    public function test_it_resolves_exactly_one_rule_for_reference_date(): void
    {
        $oldRule = new TestRule('old', EffectivePeriod::from('2024-01-01', '2024-12-31'));
        $currentRule = new TestRule('current', EffectivePeriod::from('2025-01-01'));

        $resolved = (new EffectiveRuleResolver)->resolve(
            [$oldRule, $currentRule],
            ReferenceDate::fromString('2026-07-13'),
        );

        self::assertSame($currentRule, $resolved);
    }

    public function test_it_rejects_date_without_effective_rule(): void
    {
        $this->expectException(NoEffectiveRule::class);

        (new EffectiveRuleResolver)->resolve(
            [new TestRule('future', EffectivePeriod::from('2030-01-01'))],
            ReferenceDate::fromString('2026-07-13'),
        );
    }
}

final readonly class TestRule implements EffectiveDated
{
    public function __construct(
        public string $name,
        private EffectivePeriod $period,
    ) {}

    public function effectivePeriod(): EffectivePeriod
    {
        return $this->period;
    }
}
