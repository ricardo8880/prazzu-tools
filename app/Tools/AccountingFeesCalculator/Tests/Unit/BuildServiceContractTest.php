<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Tests\Unit;

use App\Core\Quality\Attributes\CoversPlusFeature;
use App\Tools\AccountingFeesCalculator\Application\Actions\BuildServiceContract;
use PHPUnit\Framework\TestCase;

final class BuildServiceContractTest extends TestCase
{
    #[CoversPlusFeature('accounting-fee-calculator', 'service_contract')]
    public function test_it_builds_a_us_service_agreement(): void
    {
        $contract = (new BuildServiceContract)->execute([
            'client_company'=>'Example Client LLC','client_document'=>'12-3456789','client_representative'=>'Maria Client','accounting_firm'=>'Example Accounting LLC','accounting_firm_document'=>'98-7654321','accounting_representative'=>'John Accountant',
            'monthly_fee'=>'1500.00','due_day'=>10,'start_date'=>'2026-08-01','duration_months'=>12,'adjustment_index'=>'CPI-U','late_fee_percent'=>2,'termination_notice_days'=>30,'services'=>['accounting','tax'],'includes_data_privacy'=>true,'includes_confidentiality'=>true,'additional_terms'=>'Monthly review meeting.',
        ]);
        self::assertSame('Example Client LLC',$contract->clientCompany);
        self::assertSame('$1,500.00',$contract->monthlyFee->formatEnUs());
        self::assertSame('Aug 1, 2026',$contract->startsAt->format('M j, Y'));
        self::assertSame('Jul 31, 2027',$contract->endsAt->format('M j, Y'));
        self::assertTrue($contract->includesDataPrivacy);
    }
}
