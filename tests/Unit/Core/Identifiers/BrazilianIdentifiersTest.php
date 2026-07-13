<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Identifiers;

use App\Core\Identifiers\Cnpj;
use App\Core\Identifiers\Cpf;
use PHPUnit\Framework\TestCase;

final class BrazilianIdentifiersTest extends TestCase
{
    public function test_it_validates_and_formats_cpf(): void
    {
        $cpf = Cpf::fromString('529.982.247-25');

        self::assertSame('52998224725', $cpf->digits());
        self::assertSame('529.982.247-25', $cpf->formatted());
        self::assertSame('***.982.247-**', $cpf->masked());
        self::assertFalse(Cpf::isValid('111.111.111-11'));
    }

    public function test_it_validates_and_formats_cnpj(): void
    {
        $cnpj = Cnpj::fromString('11.222.333/0001-81');

        self::assertSame('11222333000181', $cnpj->digits());
        self::assertSame('11.222.333/0001-81', $cnpj->formatted());
        self::assertFalse(Cnpj::isValid('11.111.111/1111-11'));
    }
}
