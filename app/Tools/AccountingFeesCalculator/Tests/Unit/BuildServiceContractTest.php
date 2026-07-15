<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Tests\Unit;

use App\Tools\AccountingFeesCalculator\Application\Actions\BuildServiceContract;
use PHPUnit\Framework\TestCase;

final class BuildServiceContractTest extends TestCase
{
    public function test_it_builds_a_service_contract(): void
    {
        $contract = (new BuildServiceContract)->execute([
            'client_company' => 'Cliente Exemplo Ltda.',
            'client_document' => '12.345.678/0001-90',
            'client_representative' => 'Maria Cliente',
            'accounting_firm' => 'Contabilidade Exemplo',
            'accounting_firm_document' => '98.765.432/0001-10',
            'accounting_representative' => 'João Contador',
            'monthly_fee' => '1.500,00',
            'due_day' => 10,
            'start_date' => '2026-08-01',
            'duration_months' => 12,
            'adjustment_index' => 'IPCA',
            'late_fee_percent' => 2,
            'termination_notice_days' => 30,
            'services' => ['accounting', 'tax'],
            'includes_lgpd' => true,
            'includes_confidentiality' => true,
            'additional_terms' => 'Reunião mensal.',
        ]);

        self::assertSame('Cliente Exemplo Ltda.', $contract->clientCompany);
        self::assertSame('R$ 1.500,00', $contract->monthlyFee->formatPtBr());
        self::assertSame('01/08/2026', $contract->startsAt->format('d/m/Y'));
        self::assertSame('31/07/2027', $contract->endsAt->format('d/m/Y'));
        self::assertCount(2, $contract->services);
        self::assertTrue($contract->includesLgpd);
    }
}
