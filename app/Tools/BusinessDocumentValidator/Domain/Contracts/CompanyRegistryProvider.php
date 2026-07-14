<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Domain\Contracts;

use App\Tools\BusinessDocumentValidator\Domain\Data\CompanyRegistryLookupResult;

interface CompanyRegistryProvider
{
    public function lookup(string $cnpj): CompanyRegistryLookupResult;
}
