<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Tests\Unit;

use App\Tools\BusinessDocumentValidator\Domain\Enums\DocumentType;
use App\Tools\BusinessDocumentValidator\Domain\Validators\BusinessDocumentValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class BusinessDocumentValidatorTest extends TestCase
{
    #[DataProvider('validDocuments')]
    public function test_it_validates_supported_documents(
        string $document,
        DocumentType $type,
        string $expectedType,
        string $formatted,
    ): void {
        $result = (new BusinessDocumentValidator)->validate($document, $type);

        self::assertTrue($result->valid);
        self::assertSame($expectedType, $result->type->value);
        self::assertSame($formatted, $result->formatted);
    }

    /** @return iterable<string, array{string, DocumentType, string, string}> */
    public static function validDocuments(): iterable
    {
        yield 'cpf automatico com mascara' => ['529.982.247-25', DocumentType::Automatic, 'cpf', '529.982.247-25'];
        yield 'cpf explicito sem mascara' => ['52998224725', DocumentType::Cpf, 'cpf', '529.982.247-25'];
        yield 'cnpj automatico com mascara' => ['04.252.011/0001-10', DocumentType::Automatic, 'cnpj', '04.252.011/0001-10'];
        yield 'cnpj explicito sem mascara' => ['04252011000110', DocumentType::Cnpj, 'cnpj', '04.252.011/0001-10'];
    }

    #[DataProvider('invalidDocuments')]
    public function test_it_rejects_invalid_documents(string $document, DocumentType $type): void
    {
        $result = (new BusinessDocumentValidator)->validate($document, $type);

        self::assertFalse($result->valid);
        self::assertNotEmpty($result->messages);
    }

    /** @return iterable<string, array{string, DocumentType}> */
    public static function invalidDocuments(): iterable
    {
        yield 'cpf com digito incorreto' => ['52998224724', DocumentType::Cpf];
        yield 'cpf repetido' => ['11111111111', DocumentType::Automatic];
        yield 'cnpj com digito incorreto' => ['04252011000111', DocumentType::Cnpj];
        yield 'tamanho nao detectado' => ['123456', DocumentType::Automatic];
        yield 'cnpj informado como cpf' => ['04252011000110', DocumentType::Cpf];
    }
}
