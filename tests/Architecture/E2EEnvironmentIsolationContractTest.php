<?php

namespace Tests\Architecture;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class E2EEnvironmentIsolationContractTest extends TestCase
{
    #[Test]
    public function e2e_environment_template_is_safe_and_isolated(): void
    {
        $environment = $this->readEnvironment(base_path('.env.e2e.example'));

        $this->assertSame('e2e', $environment['APP_ENV']);
        $this->assertSame('sqlite', $environment['DB_CONNECTION']);
        $this->assertSame('database/e2e.sqlite', $environment['DB_DATABASE']);
        $this->assertSame('array', $environment['CACHE_STORE']);
        $this->assertSame('file', $environment['SESSION_DRIVER']);
        $this->assertSame('storage/app/e2e/sessions', $environment['SESSION_FILES_PATH']);
        $this->assertSame('sync', $environment['QUEUE_CONNECTION']);
        $this->assertSame('null', $environment['QUEUE_FAILED_DRIVER']);
        $this->assertSame('array', $environment['MAIL_MAILER']);
        $this->assertSame('e2e', $environment['FILESYSTEM_DISK']);
        $this->assertSame('false', $environment['E2E_EXTERNAL_NETWORK']);
        $this->assertStringEndsWith('/e2e', str_replace('\\', '/', $environment['E2E_STORAGE_PATH']));
    }

    #[Test]
    public function e2e_profiles_are_deterministic_and_cover_required_access_levels(): void
    {
        $profiles = config('e2e_environment.profiles');

        $this->assertSame(['free', 'plus', 'administrator'], array_keys($profiles));
        $this->assertSame('free', $profiles['free']['subscription_plan']);
        $this->assertSame('plus', $profiles['plus']['subscription_plan']);
        $this->assertSame('administrator', $profiles['administrator']['role']);
        $this->assertSame('plus', $profiles['administrator']['subscription_plan']);

        foreach ($profiles as $profile) {
            $this->assertStringEndsWith('@prazzu.test', $profile['email']);
            $this->assertNotSame('', $profile['password']);
        }
    }

    #[Test]
    public function e2e_storage_disk_and_lifecycle_commands_are_declared(): void
    {
        $this->assertSame('local', config('filesystems.disks.e2e.driver'));
        $this->assertTrue(config('filesystems.disks.e2e.throw'));
        $this->assertFileExists(base_path('scripts/e2e-environment.php'));
        $this->assertFileExists(base_path('database/seeders/E2EEnvironmentSeeder.php'));

        $composer = json_decode((string) file_get_contents(base_path('composer.json')), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame('@php scripts/e2e-environment.php prepare', $composer['scripts']['e2e:prepare']);
        $this->assertSame('@php scripts/e2e-environment.php verify', $composer['scripts']['e2e:verify']);
        $this->assertSame('@php scripts/e2e-environment.php clean', $composer['scripts']['e2e:clean']);
    }

    private function readEnvironment(string $path): array
    {
        $environment = [];

        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#') || ! str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $environment[trim($key)] = trim(trim($value), "\"'");
        }

        return $environment;
    }
}
