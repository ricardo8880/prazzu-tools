<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Normative;

use App\Core\Dates\EffectivePeriod;
use App\Core\Dates\ReferenceDate;
use App\Core\Normative\NormativeReference;
use App\Core\Normative\NormativeSourceType;
use PHPUnit\Framework\TestCase;

final class NormativeReferenceTest extends TestCase
{
    public function test_it_reports_normative_effectiveness(): void
    {
        $reference = new NormativeReference(
            NormativeSourceType::Law,
            'Lei de exemplo',
            'Regra usada somente para testar o contrato',
            ReferenceDate::fromString('2025-01-01'),
            EffectivePeriod::from('2025-02-01', '2025-12-31'),
            'https://www.gov.br/exemplo',
        );

        self::assertTrue($reference->isEffectiveOn(ReferenceDate::fromString('2025-06-01')));
        self::assertFalse($reference->isEffectiveOn(ReferenceDate::fromString('2026-01-01')));
    }
}
