<?php

namespace Tests\Unit\Analytics;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Tests\TestCase;

final class AnalyticsArchitectureTest extends TestCase
{
    public function test_tools_do_not_depend_on_analytics_infrastructure_or_models(): void
    {
        $violations = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(app_path('Tools')));

        foreach ($iterator as $file) {
            if (! $file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $contents = file_get_contents($file->getPathname()) ?: '';

            if (str_contains($contents, 'Core\\Analytics\\Infrastructure') || str_contains($contents, 'Core\\Analytics\\Models')) {
                $violations[] = str_replace(base_path().'/', '', $file->getPathname());
            }
        }

        self::assertSame([], $violations, 'Ferramentas devem conhecer apenas contratos e objetos públicos do Core Analytics.');
    }

    public function test_analytics_views_do_not_query_models_directly(): void
    {
        $violations = [];
        $path = resource_path('views/admin/analytics');

        foreach (glob($path.'/*.blade.php') ?: [] as $file) {
            $contents = file_get_contents($file) ?: '';

            if (str_contains($contents, '::query(') || str_contains($contents, 'DB::')) {
                $violations[] = str_replace(base_path().'/', '', $file);
            }
        }

        self::assertSame([], $violations, 'Views de Analytics não devem consultar banco diretamente.');
    }
}
