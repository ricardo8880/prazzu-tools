<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Tests\Unit;

use App\Core\Tools\Enums\ToolCategory;
use App\Core\Tools\Enums\ToolStatus;
use App\Tools\BusinessDocumentValidator\Tool;
use PHPUnit\Framework\TestCase;

final class ToolManifestTest extends TestCase
{
    public function test_manifest_exposes_the_business_document_validator(): void
    {
        $manifest = (new Tool)->manifest();

        self::assertSame('validador-de-cnpj', $manifest->slug);
        self::assertSame('Validador Inteligente de CNPJ, CPF e IE', $manifest->name);
        self::assertSame(ToolCategory::Validators, $manifest->category);
        self::assertSame(ToolStatus::Active, $manifest->status);
        self::assertSame('1.0.0', $manifest->version);
        self::assertSame('tools.validador-de-cnpj.index', $manifest->routeName);
        self::assertTrue($manifest->supportsHistory);
        self::assertTrue($manifest->storesSensitiveData);
    }
}
