<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Tests\Unit;

use App\Tools\BusinessDocumentValidator\Application\Actions\BuildBatchExportRows;
use PHPUnit\Framework\TestCase;

final class BuildBatchExportRowsTest extends TestCase
{
    public function test_it_exports_only_problematic_rows_when_requested(): void
    {
        $result = ['rows' => [
            ['line' => 2, 'document' => '1', 'formatted_document' => '1', 'type' => 'CPF', 'valid' => true, 'duplicate' => false, 'registry_status' => null, 'company' => null, 'inconsistencies' => [], 'message' => 'ok'],
            ['line' => 3, 'document' => '2', 'formatted_document' => '2', 'type' => 'CNPJ', 'valid' => false, 'duplicate' => false, 'registry_status' => null, 'company' => null, 'inconsistencies' => [], 'message' => 'inválido'],
        ]];

        $rows = (new BuildBatchExportRows)->execute($result, true);

        self::assertCount(1, $rows);
        self::assertSame(3, $rows[0][0]);
    }
}
