<?php

namespace Tests\Unit\Core\Quality\E2E;

use App\Core\Quality\E2E\Support\TestId;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TestIdTest extends TestCase
{
    #[DataProvider('identifiers')]
    public function test_it_builds_stable_ascii_identifiers(string $prefix, string $value, string $expected): void
    {
        self::assertSame($expected, TestId::make($prefix, $value));
    }

    public static function identifiers(): array
    {
        return [
            ['tool-page', 'custo-funcionario-clt', 'tool-page-custo-funcionario-clt'],
            ['field', 'scenarios[0][monthly_revenue]', 'field-scenarios-0-monthly-revenue'],
            ['download', 'Exportar PDF', 'download-exportar-pdf'],
            ['action', 'Pró-Labore', 'action-pro-labore'],
        ];
    }

    public function test_field_uses_the_official_prefix(): void
    {
        self::assertSame('field-company-document', TestId::field('company.document'));
    }
}
