<?php

declare(strict_types=1);

namespace Tests\Unit\Distribution;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class DistributionVerifierTest extends TestCase
{
    private string $temporaryRoot;

    protected function setUp(): void
    {
        parent::setUp();

        $this->temporaryRoot = sys_get_temp_dir().'/prazzu-distribution-test-'.bin2hex(random_bytes(6));
        mkdir($this->temporaryRoot, 0777, true);
    }

    protected function tearDown(): void
    {
        $this->deleteDirectory($this->temporaryRoot);

        parent::tearDown();
    }

    public function test_clean_source_package_with_environment_examples_is_accepted(): void
    {
        file_put_contents($this->temporaryRoot.'/.env.example', 'APP_ENV=production');
        file_put_contents($this->temporaryRoot.'/.env.e2e.example', 'APP_ENV=e2e');
        file_put_contents($this->temporaryRoot.'/README.md', '# Prazzu Tools');

        [$exitCode, $output] = $this->verify();

        self::assertSame(0, $exitCode, $output);
        self::assertStringContainsString('validado sem resíduos locais', $output);
    }

    #[DataProvider('forbiddenLocalDataProvider')]
    public function test_local_databases_and_database_dumps_are_rejected(string $relativePath): void
    {
        $absolutePath = $this->temporaryRoot.'/'.$relativePath;
        $directory = dirname($absolutePath);

        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        file_put_contents($absolutePath, 'local-only');

        [$exitCode, $output] = $this->verify();

        self::assertSame(1, $exitCode);
        self::assertStringContainsString($relativePath, $output);
    }

    /** @return array<string, array{string}> */
    public static function forbiddenLocalDataProvider(): array
    {
        return [
            'e2e sqlite' => ['database/e2e.sqlite'],
            'sqlite wal' => ['database/e2e.sqlite-wal'],
            'sqlite shm' => ['database/e2e.sqlite-shm'],
            'other sqlite' => ['storage/local.sqlite3'],
            'sql dump outside backup directory' => ['prazzu-export.sql'],
            'compressed sql dump' => ['exports/prazzu-export.sql.gz'],
            'database dump' => ['exports/prazzu.dump'],
            'backup extension' => ['exports/prazzu.bak'],
        ];
    }

    /** @return array{int, string} */
    private function verify(): array
    {
        $script = dirname(__DIR__, 3).'/scripts/verify-distribution.php';
        $command = escapeshellarg(PHP_BINARY).' '.escapeshellarg($script).' '.escapeshellarg($this->temporaryRoot).' 2>&1';
        $lines = [];
        $exitCode = 0;

        exec($command, $lines, $exitCode);

        return [$exitCode, implode("\n", $lines)];
    }

    private function deleteDirectory(string $directory): void
    {
        if (! is_dir($directory)) {
            return;
        }

        $items = array_diff(scandir($directory) ?: [], ['.', '..']);

        foreach ($items as $item) {
            $path = $directory.'/'.$item;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }

        rmdir($directory);
    }
}
