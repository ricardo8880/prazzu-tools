<?php

declare(strict_types=1);

namespace App\Tools\FederalPaymentGuideGenerator\Tests\Unit;

use App\Core\Money\Money;
use App\Tools\FederalPaymentGuideGenerator\Domain\Data\LatePaymentInput;
use App\Tools\FederalPaymentGuideGenerator\Domain\Services\LatePaymentCalculator;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class LatePaymentCalculatorTest extends TestCase
{
    public function test_it_caps_penalty_at_twenty_percent(): void
    {
        $result = (new LatePaymentCalculator())->calculate(new LatePaymentInput(
            Money::fromMinor(100000),
            new DateTimeImmutable('2026-01-01'),
            new DateTimeImmutable('2026-04-11'),
            '5',
        ));

        self::assertSame('20.00', $result->penaltyPercent);
        self::assertSame(20000, $result->penalty->minorAmount());
        self::assertSame(125000, $result->total->minorAmount());
    }

    public function test_result_carries_normative_metadata_for_history_reproduction(): void
    {
        $result = (new LatePaymentCalculator)->calculate(new LatePaymentInput(
            Money::fromMinor(100000),
            new DateTimeImmutable('2026-01-01'),
            new DateTimeImmutable('2026-01-11'),
            '1',
        ));

        self::assertSame('federal_payment_guide.late_payment_charges', $result->normativeRule['identifier']);
        self::assertSame('2026.1.0', $result->normativeRule['version']);
        self::assertNotEmpty($result->normativeRule['references']);
    }
}
