<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Tests\Unit;

use App\Tools\AccountingFeesCalculator\Application\Actions\BuildCommercialProposal;
use PHPUnit\Framework\TestCase;

final class BuildCommercialProposalTest extends TestCase
{
    public function test_builds_a_printable_commercial_proposal(): void
    {
        $proposal = (new BuildCommercialProposal)->execute([
            'client_company' => 'Empresa Exemplo Ltda.',
            'client_document' => '12.345.678/0001-90',
            'contact_name' => 'Maria Silva',
            'accounting_firm' => 'Contabilidade Modelo',
            'monthly_fee' => '1.930,19',
            'setup_fee' => '500,00',
            'due_day' => 10,
            'validity_days' => 15,
            'services' => ['accounting', 'tax', 'payroll'],
            'notes' => 'Reunião mensal incluída.',
        ]);

        self::assertSame('Empresa Exemplo Ltda.', $proposal->clientCompany);
        self::assertSame('R$ 1.930,19', $proposal->monthlyFee->formatPtBr());
        self::assertSame('R$ 500,00', $proposal->setupFee->formatPtBr());
        self::assertSame(10, $proposal->dueDay);
        self::assertCount(3, $proposal->services);
        self::assertSame('Escrituração contábil e demonstrações', $proposal->services[0]);
        self::assertSame(15, $proposal->issuedAt->diff($proposal->validUntil)->days);
    }

    public function test_empty_setup_fee_is_treated_as_zero(): void
    {
        $proposal = (new BuildCommercialProposal)->execute([
            'client_company' => 'Empresa Exemplo Ltda.',
            'client_document' => null,
            'contact_name' => 'Maria Silva',
            'accounting_firm' => 'Contabilidade Modelo',
            'monthly_fee' => '1.000,00',
            'setup_fee' => '',
            'due_day' => 10,
            'validity_days' => 30,
            'services' => ['accounting'],
            'notes' => null,
        ]);

        self::assertSame('R$ 0,00', $proposal->setupFee->formatPtBr());
    }
}
