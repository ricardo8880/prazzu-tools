<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Tests\Unit;

use App\Tools\BusinessDocumentValidator\Domain\Analyzers\CompanyConsistencyAnalyzer;
use App\Tools\BusinessDocumentValidator\Domain\Data\CompanyConsistencyInput;
use App\Tools\BusinessDocumentValidator\Domain\Data\CompanyRegistryData;
use App\Tools\BusinessDocumentValidator\Domain\Data\CompanyRegistryLookupResult;
use PHPUnit\Framework\TestCase;

final class CompanyConsistencyAnalyzerTest extends TestCase
{
    public function test_it_reports_deterministic_inconsistencies(): void
    {
        $result = (new CompanyConsistencyAnalyzer())->analyze(
            new CompanyConsistencyInput(
                cnpj: '04.252.011/0001-10',
                legalName: 'OUTRA EMPRESA LTDA',
                tradeName: 'EXEMPLO',
                state: 'RJ',
                city: 'RIO DE JANEIRO',
                stateRegistration: null,
            ),
            CompanyRegistryLookupResult::found($this->company()),
        );

        self::assertTrue($result->completed);
        self::assertSame(1, $result->errorCount());
        self::assertSame(2, $result->warningCount());
        self::assertTrue($result->hasProblems());
        self::assertSame(
            ['legal_name_mismatch', 'city_mismatch', 'state_mismatch'],
            array_map(static fn ($item): string => $item->code, $result->inconsistencies),
        );
    }

    public function test_it_accepts_equivalent_text_ignoring_case_accents_and_punctuation(): void
    {
        $result = (new CompanyConsistencyAnalyzer())->analyze(
            new CompanyConsistencyInput(
                cnpj: '04.252.011/0001-10',
                legalName: 'empresa exemplo ltda.',
                tradeName: 'exemplo',
                state: 'SP',
                city: 'São Paulo',
                stateRegistration: null,
            ),
            CompanyRegistryLookupResult::found($this->company()),
        );

        self::assertFalse($result->hasProblems());
        self::assertSame('no_inconsistencies_found', $result->inconsistencies[0]->code);
    }

    public function test_it_does_not_treat_provider_unavailability_as_company_irregularity(): void
    {
        $result = (new CompanyConsistencyAnalyzer())->analyze(
            new CompanyConsistencyInput('04.252.011/0001-10', null, null, null, null, null),
            CompanyRegistryLookupResult::unavailable(),
        );

        self::assertFalse($result->completed);
        self::assertSame(0, $result->errorCount());
        self::assertSame('registry_lookup_incomplete', $result->inconsistencies[0]->code);
    }

    private function company(): CompanyRegistryData
    {
        return new CompanyRegistryData(
            cnpj: '04252011000110',
            legalName: 'EMPRESA EXEMPLO LTDA',
            tradeName: 'EXEMPLO',
            registrationStatus: 'ATIVA',
            registrationStatusDate: '2020-01-01',
            openingDate: '2010-01-01',
            legalNature: 'Sociedade Empresária Limitada',
            branchType: 'MATRIZ',
            primaryActivity: null,
            secondaryActivities: [],
            street: 'RUA EXEMPLO',
            number: '100',
            complement: null,
            district: 'CENTRO',
            city: 'SAO PAULO',
            state: 'SP',
            postalCode: '01001000',
            source: 'Teste',
            consultedAt: '2026-07-14T12:00:00-03:00',
        );
    }
}
