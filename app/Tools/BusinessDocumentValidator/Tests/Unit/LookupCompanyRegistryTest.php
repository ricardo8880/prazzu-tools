<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Tests\Unit;

use App\Tools\BusinessDocumentValidator\Application\Actions\LookupCompanyRegistry;
use App\Tools\BusinessDocumentValidator\Domain\Contracts\CompanyRegistryProvider;
use App\Tools\BusinessDocumentValidator\Domain\Data\CompanyRegistryLookupResult;
use App\Tools\BusinessDocumentValidator\Domain\Validators\BusinessDocumentValidator;
use PHPUnit\Framework\TestCase;

final class LookupCompanyRegistryTest extends TestCase
{
    public function test_it_does_not_call_provider_for_invalid_cnpj(): void
    {
        $provider = new class implements CompanyRegistryProvider
        {
            public bool $called = false;

            public function lookup(string $cnpj): CompanyRegistryLookupResult
            {
                $this->called = true;

                return CompanyRegistryLookupResult::unavailable();
            }
        };

        $result = (new LookupCompanyRegistry(new BusinessDocumentValidator(), $provider))
            ->execute('11.111.111/1111-11');

        self::assertFalse($provider->called);
        self::assertSame('not_found', $result->status->value);
        self::assertStringContainsString('matematicamente inválido', $result->message);
    }

    public function test_it_normalizes_cnpj_before_calling_provider(): void
    {
        $provider = new class implements CompanyRegistryProvider
        {
            public ?string $received = null;

            public function lookup(string $cnpj): CompanyRegistryLookupResult
            {
                $this->received = $cnpj;

                return CompanyRegistryLookupResult::notFound();
            }
        };

        (new LookupCompanyRegistry(new BusinessDocumentValidator(), $provider))
            ->execute('04.252.011/0001-10');

        self::assertSame('04252011000110', $provider->received);
    }
}
