<?php

declare(strict_types=1);

namespace App\Tools\DigitalCertificateAnalyzer\Tests\Unit;

use App\Tools\DigitalCertificateAnalyzer\Application\Data\CalculationInput;
use App\Tools\DigitalCertificateAnalyzer\Domain\Services\Calculator;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    private function fixture(): string
    {
        $contents = file_get_contents(dirname(__DIR__).'/Fixtures/certificate-e2e.p12');
        self::assertIsString($contents);
        return $contents;
    }

    public function test_it_reads_a_pkcs12_without_serializing_secrets(): void
    {
        $input = new CalculationInput($this->fixture(), 'prazzu-e2e-2026', 'certificado.p12', strlen($this->fixture()), new DateTimeImmutable('2026-08-14T12:00:00-03:00'), true);
        $result = (new Calculator)->calculate($input);

        self::assertSame('analisador-certificado-digital-a1', $result->toolSlug);
        self::assertSame('CNPJ', $result->details['holder']['document_type']);
        self::assertSame('11.222.333/0001-81', $result->details['holder']['document']);
        self::assertNotEmpty($result->details['technical']['sha256_fingerprint']);
        self::assertArrayNotHasKey('password', $input->toArray());
        self::assertArrayNotHasKey('pkcs12', $input->toArray());
        self::assertStringNotContainsString('prazzu-e2e-2026', json_encode($result->toArray(), JSON_THROW_ON_ERROR));
    }

    public function test_it_rejects_a_wrong_password_with_a_controlled_message(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Confira se o arquivo é .pfx/.p12 válido e se a senha está correta.');
        (new Calculator)->calculate(new CalculationInput($this->fixture(), 'senha-incorreta', 'certificado.p12', strlen($this->fixture()), new DateTimeImmutable('2026-08-14T12:00:00-03:00')));
    }

    public function test_essential_mode_omits_plus_technical_details(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput($this->fixture(), 'prazzu-e2e-2026', 'certificado.p12', strlen($this->fixture()), new DateTimeImmutable('2026-08-14T12:00:00-03:00'), false));
        self::assertArrayNotHasKey('technical', $result->details);
    }
}
