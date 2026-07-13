<?php

namespace Tests\Unit\Core\Tools\History;

use App\Core\Tools\History\Data\ToolHistoryPolicy;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ToolHistoryPolicyTest extends TestCase
{
    public function test_active_policy_requires_retention(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ToolHistoryPolicy(enabled: true);
    }

    public function test_sensitive_fields_must_be_allowed(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ToolHistoryPolicy(
            enabled: true,
            retentionDays: 30,
            inputFields: ['amount'],
            sensitiveFields: ['cpf'],
        );
    }

    public function test_valid_policy_is_created(): void
    {
        $policy = new ToolHistoryPolicy(
            enabled: true,
            retentionDays: 90,
            inputFields: ['employee.salary', 'employee.cpf'],
            resultFields: ['total'],
            sensitiveFields: ['employee.cpf'],
        );

        self::assertSame(90, $policy->retentionDays);
        self::assertSame(['employee.cpf'], $policy->sensitiveFields);
    }
}
