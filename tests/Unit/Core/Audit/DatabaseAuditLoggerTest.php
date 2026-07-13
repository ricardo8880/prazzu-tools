<?php

namespace Tests\Unit\Core\Audit;

use App\Core\Audit\Contracts\AuditLogger;
use App\Core\Audit\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class DatabaseAuditLoggerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_records_encrypted_metadata(): void
    {
        $log = app(AuditLogger::class)->record(
            action: 'tool_run.started',
            auditableType: 'tool_run',
            auditableId: 'run-1',
            metadata: ['tool_slug' => 'calculator'],
        );

        self::assertSame(['tool_slug' => 'calculator'], $log->metadata);
        self::assertNotSame(json_encode($log->metadata), AuditLog::query()->value('metadata'));
    }
}
