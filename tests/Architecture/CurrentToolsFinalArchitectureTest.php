<?php

namespace Tests\Architecture;

use App\Core\Tools\Contracts\HasToolIntegrations;
use App\Core\Tools\Contracts\HasViews;
use App\Core\Tools\Enums\ToolCapability;
use App\Core\Tools\Enums\ToolFeatureTier;
use App\Core\Tools\History\Contracts\HasHistoryPolicy;
use App\Core\Tools\Infrastructure\Enums\SensitiveDataMode;
use App\Core\Tools\ToolRegistry;
use Tests\TestCase;

final class CurrentToolsFinalArchitectureTest extends TestCase
{
    public function test_all_current_tools_follow_the_final_manifest_and_transversal_policies(): void
    {
        $tools = app(ToolRegistry::class)->all();

        $this->assertCount(count(array_merge(...array_values(config('tools.modules')))), $tools);

        foreach ($tools as $tool) {
            $manifest = $tool->manifest();

            $this->assertNotEmpty($manifest->featuresFor(ToolFeatureTier::Essential), $manifest->slug);
            $this->assertNotEmpty($manifest->featuresFor(ToolFeatureTier::Plus), $manifest->slug);
            $this->assertInstanceOf(HasHistoryPolicy::class, $tool, $manifest->slug);
            $this->assertTrue($manifest->hasCapability(ToolCapability::History), $manifest->slug);
            $this->assertTrue($manifest->hasCapability(ToolCapability::VersionedPersistence), $manifest->slug);
            $this->assertTrue($manifest->hasCapability(ToolCapability::Export), $manifest->slug);
            $this->assertTrue($manifest->persistence?->enabled, $manifest->slug);
            $this->assertSame(1, $manifest->persistence?->schemaVersion, $manifest->slug);
            $this->assertTrue($manifest->export?->enabled, $manifest->slug);
            $this->assertContains('json', $manifest->export?->formats ?? [], $manifest->slug);
            $this->assertFalse($manifest->sharing?->enabled ?? true, $manifest->slug);

            $sensitiveMode = $manifest->sensitiveData?->mode ?? SensitiveDataMode::None;
            $this->assertSame(
                $manifest->storesSensitiveData,
                $sensitiveMode !== SensitiveDataMode::None,
                $manifest->slug,
            );

            if ($tool instanceof HasViews) {
                $views = glob($tool->viewsPath().'/**/*.blade.php') ?: [];
                $views = [...$views, ...(glob($tool->viewsPath().'/*.blade.php') ?: [])];
                $source = implode("\n", array_map(static fn (string $path): string => (string) file_get_contents($path), $views));

                $this->assertStringContainsString('<x-tools.', $source, $manifest->slug);
            }

            if ($tool instanceof HasToolIntegrations) {
                $integrationManifest = $tool->integrations();
                $this->assertSame(
                    $integrationManifest->publishes !== [],
                    $manifest->hasCapability(ToolCapability::PublishesIntegrations),
                    $manifest->slug,
                );
                $this->assertSame(
                    $integrationManifest->accepts !== [],
                    $manifest->hasCapability(ToolCapability::AcceptsIntegrations),
                    $manifest->slug,
                );
            }
        }
    }
}
