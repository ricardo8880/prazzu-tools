<?php

namespace Tests\Architecture;

use App\Core\Tools\Contracts\ToolModule;
use App\Core\Tools\Data\ToolManifest;
use App\Core\Tools\Enums\ToolCategory;
use App\Core\Tools\Infrastructure\Data\ToolExportPolicy;
use App\Core\Tools\Infrastructure\Data\ToolPersistencePolicy;
use App\Core\Tools\Infrastructure\Data\ToolSensitiveDataPolicy;
use App\Core\Tools\Infrastructure\Data\ToolSharingPolicy;
use App\Core\Tools\Infrastructure\Enums\SensitiveDataMode;
use App\Core\Tools\Infrastructure\Services\ManifestSensitiveToolPayloadProtector;
use App\Core\Tools\Infrastructure\Services\ManifestToolResultCompatibility;
use App\Core\Tools\Infrastructure\Services\ManifestToolResultExporter;
use App\Core\Tools\Infrastructure\Services\ManifestToolResultSharingGuard;
use DomainException;
use Tests\TestCase;

final class TransversalToolInfrastructureTest extends TestCase
{
    public function test_manifest_policies_are_serializable_without_forcing_tool_migration(): void
    {
        $legacy = $this->module(new ToolManifest(
            slug: 'legacy-tool',
            name: 'Legacy Tool',
            description: 'Ferramenta ainda não migrada.',
            category: ToolCategory::Calculators,
            icon: 'calculator',
            routeName: 'tools.legacy-tool.index',
        ));

        self::assertNull($legacy->manifest()->persistence);
        self::assertNull($legacy->manifest()->export);
        self::assertNull($legacy->manifest()->sharing);
        self::assertNull($legacy->manifest()->sensitiveData);

        $manifest = ToolManifest::fromArray($this->manifest()->toArray());

        self::assertSame(2, $manifest->persistence?->schemaVersion);
        self::assertSame(['json', 'csv'], $manifest->export?->formats);
        self::assertSame(120, $manifest->sharing?->expiresAfterMinutes);
        self::assertSame(SensitiveDataMode::Redacted, $manifest->sensitiveData?->mode);
    }

    public function test_compatibility_respects_schema_range_and_semantic_major_version(): void
    {
        $service = new ManifestToolResultCompatibility();
        $module = $this->module($this->manifest());

        self::assertTrue($service->canRead($module, '1.4.0', 1));
        self::assertTrue($service->canRead($module, '1.0.0', 2));
        self::assertFalse($service->canRead($module, '2.0.0', 2));
        self::assertFalse($service->canRead($module, '1.4.0', 3));
    }

    public function test_export_sharing_and_sensitive_data_follow_manifest_policies(): void
    {
        $module = $this->module($this->manifest());
        $exporter = new ManifestToolResultExporter();
        $sharing = new ManifestToolResultSharingGuard();
        $protector = new ManifestSensitiveToolPayloadProtector();

        self::assertJson($exporter->export($module, ['total' => 10], 'json'));
        self::assertStringContainsString('total', $exporter->export($module, ['total' => 10], 'csv'));
        self::assertSame(120, $sharing->expirationMinutes($module));
        self::assertSame(
            ['document' => '[REDACTED]', 'total' => 10],
            $protector->protect($module, ['document' => '123', 'total' => 10]),
        );

        $this->expectException(DomainException::class);
        $sharing->authorize($module, authenticated: false, containsSensitivePayload: false);
    }

    private function manifest(): ToolManifest
    {
        return new ToolManifest(
            slug: 'transversal-test',
            name: 'Transversal Test',
            description: 'Manifesto usado nos testes da infraestrutura transversal.',
            category: ToolCategory::Calculators,
            icon: 'calculator',
            routeName: 'tools.transversal-test.index',
            version: '1.5.0',
            supportsHistory: true,
            storesSensitiveData: true,
            persistence: new ToolPersistencePolicy(
                enabled: true,
                schemaVersion: 2,
                retentionDays: 90,
                minimumReadableSchemaVersion: 1,
            ),
            export: new ToolExportPolicy(enabled: true, formats: ['json', 'csv']),
            sharing: new ToolSharingPolicy(
                enabled: true,
                expiresAfterMinutes: 120,
                requiresAuthentication: true,
                allowSensitivePayload: false,
            ),
            sensitiveData: new ToolSensitiveDataPolicy(
                mode: SensitiveDataMode::Redacted,
                fields: ['document'],
            ),
        );
    }

    private function module(ToolManifest $manifest): ToolModule
    {
        return new class($manifest) implements ToolModule
        {
            public function __construct(private readonly ToolManifest $manifest) {}

            public function manifest(): ToolManifest
            {
                return $this->manifest;
            }
        };
    }
}
