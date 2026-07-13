<?php

namespace Tests\Unit\Core\Tools\History;

use App\Core\Tools\History\Services\PayloadProjector;
use PHPUnit\Framework\TestCase;

final class PayloadProjectorTest extends TestCase
{
    public function test_only_explicitly_allowed_fields_are_projected(): void
    {
        $payload = [
            'employee' => ['name' => 'Ana', 'cpf' => '123', 'salary' => '5000'],
            'internal_token' => 'do-not-store',
        ];

        $projected = (new PayloadProjector())->project($payload, [
            'employee.name',
            'employee.salary',
        ]);

        self::assertSame([
            'employee' => ['name' => 'Ana', 'salary' => '5000'],
        ], $projected);
    }
}
