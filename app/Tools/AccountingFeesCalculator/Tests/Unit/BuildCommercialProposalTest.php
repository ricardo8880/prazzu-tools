<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Tests\Unit;

use App\Core\Quality\Attributes\CoversPlusFeature;
use App\Tools\AccountingFeesCalculator\Application\Actions\BuildCommercialProposal;
use PHPUnit\Framework\TestCase;

final class BuildCommercialProposalTest extends TestCase
{
    #[CoversPlusFeature('accounting-fee-calculator', 'commercial_proposal')]
    public function test_builds_a_printable_commercial_proposal(): void
    {
        $proposal = (new BuildCommercialProposal)->execute([
            'client_company' => 'Example Company LLC',
            'client_document' => '12-3456789',
            'contact_name' => 'Maria Smith',
            'accounting_firm' => 'Example Accounting LLC',
            'monthly_fee' => '1930.19',
            'setup_fee' => '500.00',
            'due_day' => 10,
            'validity_days' => 15,
            'services' => ['accounting', 'tax', 'payroll'],
            'notes' => 'Monthly review meeting included.',
        ]);

        self::assertSame('Example Company LLC', $proposal->clientCompany);
        self::assertSame('$1,930.19', $proposal->monthlyFee->formatEnUs());
        self::assertSame('$500.00', $proposal->setupFee->formatEnUs());
        self::assertSame(10, $proposal->dueDay);
        self::assertCount(3, $proposal->services);
        self::assertSame('Bookkeeping and financial statements', $proposal->services[0]);
        self::assertSame(15, $proposal->issuedAt->diff($proposal->validUntil)->days);
    }

    public function test_empty_setup_fee_is_treated_as_zero(): void
    {
        $proposal = (new BuildCommercialProposal)->execute([
            'client_company' => 'Example Company LLC',
            'client_document' => null,
            'contact_name' => 'Maria Smith',
            'accounting_firm' => 'Example Accounting LLC',
            'monthly_fee' => '1000.00',
            'setup_fee' => '',
            'due_day' => 10,
            'validity_days' => 30,
            'services' => ['accounting'],
            'notes' => null,
        ]);

        self::assertSame('$0.00', $proposal->setupFee->formatEnUs());
    }
}
