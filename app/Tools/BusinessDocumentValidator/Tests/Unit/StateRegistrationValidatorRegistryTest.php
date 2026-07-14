<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Tests\Unit;

use App\Tools\BusinessDocumentValidator\Domain\Validators\StateRegistrationValidatorRegistry;
use App\Tools\BusinessDocumentValidator\Providers\BusinessDocumentValidatorServiceProvider;
use Tests\TestCase;

final class StateRegistrationValidatorRegistryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->app->register(BusinessDocumentValidatorServiceProvider::class);
    }

    public function test_validates_known_state_registrations(): void
    {
        $registry = $this->app->make(StateRegistrationValidatorRegistry::class);

        self::assertTrue($registry->validate('110.042.490.114', 'SP')->valid);
        self::assertTrue($registry->validate('062.307.904/0081', 'MG')->valid);
        self::assertTrue($registry->validate('12.345.67-4', 'RJ')->valid);
        self::assertTrue($registry->validate('123.45678-50', 'PR')->valid);
    }

    public function test_rejects_invalid_registration_for_selected_state(): void
    {
        $result = $this->app->make(StateRegistrationValidatorRegistry::class)
            ->validate('110042490115', 'SP');

        self::assertFalse($result->valid);
        self::assertTrue($result->supported);
        self::assertSame('SP', $result->state);
    }

    public function test_reports_unsupported_state_without_guessing(): void
    {
        $result = $this->app->make(StateRegistrationValidatorRegistry::class)
            ->validate('123456789', 'AC');

        self::assertFalse($result->valid);
        self::assertFalse($result->supported);
    }
}
