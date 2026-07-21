<?php

declare(strict_types=1);

namespace App\Tools\FederalPaymentGuideGenerator\Tests\Unit;

use App\Tools\FederalPaymentGuideGenerator\Application\Actions\PrepareGuideExport;
use PHPUnit\Framework\TestCase;

final class PrepareGuideExportTest extends TestCase
{
    public function test_it_prepares_stable_rows_and_payload_without_numeric_floats(): void
    {
        $export = (new PrepareGuideExport())->execute(
            ['guide_type' => 'darf', 'revenue_code' => '0561', 'payment_date' => '2026-07-21'],
            [
                'guide' => ['type' => 'DARF', 'code' => '0561', 'description' => 'Rendimentos do trabalho', 'periodicity' => 'mensal', 'official_reference' => 'Catálogo'],
                'dates' => ['due_date' => '2026-07-10', 'payment_date' => '2026-07-21'],
                'amounts' => ['principal' => 'R$ 1.000,00', 'penalty' => 'R$ 33,00', 'interest' => 'R$ 10,00', 'total' => 'R$ 1.043,00'],
                'calculation' => ['calendar_days_late' => 11, 'penalty_percent' => '3.63', 'interest_percent' => '1'],
                'warnings' => ['Confirme no sistema oficial.'],
            ],
        );

        self::assertSame('darf-gps-darf-0561-2026-07-21', $export['filename']);
        self::assertSame(['Campo', 'Valor'], $export['headers']);
        self::assertSame('R$ 1.043,00', $export['summary']);
        self::assertSame('3.63 %', $export['rows'][12][1]);
        self::assertSame('Confirme no sistema oficial.', $export['rows'][14][1]);
        self::assertSame('0561', $export['payload']['result']['guide']['code']);
    }
}
