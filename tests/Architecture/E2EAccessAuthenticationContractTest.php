<?php

declare(strict_types=1);

namespace Tests\Architecture;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class E2EAccessAuthenticationContractTest extends TestCase
{
    #[Test]
    public function lot_nine_declares_reusable_profiles_and_transversal_runner(): void
    {
        $environment = require base_path('config/e2e_environment.php');

        self::assertSame(['free', 'plus', 'administrator'], array_keys($environment['profiles']));
        self::assertSame('free', $environment['profiles']['free']['subscription_plan']);
        self::assertSame('plus', $environment['profiles']['plus']['subscription_plan']);
        self::assertSame('administrator', $environment['profiles']['administrator']['role']);

        self::assertFileExists(base_path('scripts/e2e-access.php'));
        self::assertFileExists(base_path('tests/Browser/playwright/auth.setup.spec.ts'));
        self::assertFileExists(base_path('tests/Browser/playwright/tool-access.spec.ts'));
        self::assertFileExists(base_path('tests/Browser/playwright/helpers/access-profiles.ts'));
    }

    #[Test]
    public function e2e_sessions_are_persistent_and_isolated(): void
    {
        $example = (string) file_get_contents(base_path('.env.e2e.example'));
        $session = (string) file_get_contents(config_path('session.php'));

        self::assertStringContainsString('SESSION_DRIVER=file', $example);
        self::assertStringContainsString('SESSION_FILES_PATH=storage/app/e2e/sessions', $example);
        self::assertStringContainsString("env('SESSION_FILES_PATH'", $session);
    }
}
