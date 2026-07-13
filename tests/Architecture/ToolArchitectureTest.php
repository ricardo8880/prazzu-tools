<?php

namespace Tests\Architecture;

use App\Core\Quality\Services\ArchitectureInspector;
use Tests\TestCase;

final class ToolArchitectureTest extends TestCase
{
    public function test_registered_tools_respect_architecture_rules(): void
    {
        $violations = $this->app->make(ArchitectureInspector::class)->inspect();
        $messages = array_map(static fn ($violation): string => $violation->format(), $violations);

        self::assertSame([], $messages, implode(PHP_EOL, $messages));
    }
}
