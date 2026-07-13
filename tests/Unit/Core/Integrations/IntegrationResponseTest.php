<?php

namespace Tests\Unit\Core\Integrations;

use App\Core\Integrations\Data\IntegrationResponse;
use PHPUnit\Framework\TestCase;

final class IntegrationResponseTest extends TestCase
{
    public function test_only_two_hundred_responses_are_successful(): void
    {
        self::assertTrue((new IntegrationResponse(200, []))->successful());
        self::assertTrue((new IntegrationResponse(299, []))->successful());
        self::assertFalse((new IntegrationResponse(300, []))->successful());
        self::assertFalse((new IntegrationResponse(500, []))->successful());
    }
}
