<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Tests\Unit;

use App\Tools\TaxRegimeComparator\Domain\Enums\BusinessActivity;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class BusinessActivityTest extends TestCase
{
    #[DataProvider('activities')]
    public function test_it_exposes_a_user_facing_label(BusinessActivity $activity, string $label): void
    {
        self::assertSame($label, $activity->label());
    }

    /** @return iterable<string, array{BusinessActivity, string}> */
    public static function activities(): iterable
    {
        yield 'commerce' => [BusinessActivity::Commerce, 'Comércio'];
        yield 'industry' => [BusinessActivity::Industry, 'Indústria'];
        yield 'services' => [BusinessActivity::Services, 'Prestação de serviços'];
        yield 'accounting' => [BusinessActivity::AccountingServices, 'Serviços contábeis'];
        yield 'mixed' => [BusinessActivity::Mixed, 'Atividade mista'];
    }
}
