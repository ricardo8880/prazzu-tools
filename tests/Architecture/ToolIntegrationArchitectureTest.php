<?php

declare(strict_types=1);

namespace Tests\Architecture;

use App\Core\ToolIntegration\Contracts\ToolIntegrationCatalog;
use App\Core\Tools\Contracts\HasToolIntegrations;
use App\Core\Tools\ToolRegistry;
use ReflectionClass;
use Tests\TestCase;

final class ToolIntegrationArchitectureTest extends TestCase
{
    public function test_every_declared_contract_is_registered_in_the_core(): void
    {
        $catalog = app(ToolIntegrationCatalog::class);

        foreach (app(ToolRegistry::class)->modules() as $module) {
            if (! $module instanceof HasToolIntegrations) {
                continue;
            }

            foreach ([...$module->integrations()->publishes, ...$module->integrations()->accepts] as $contractKey) {
                self::assertNotNull(
                    $catalog->find(...$this->contractIdentity($contractKey)),
                    "A ferramenta [{$module->manifest()->slug}] declara o contrato não registrado [{$contractKey}].",
                );
            }
        }
    }

    public function test_tools_do_not_import_internal_classes_from_other_tools(): void
    {
        foreach (app(ToolRegistry::class)->modules() as $module) {
            $reflection = new ReflectionClass($module);
            $moduleRoot = dirname((string) $reflection->getFileName());
            $ownNamespace = $reflection->getNamespaceName().'\\';

            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($moduleRoot));

            foreach ($iterator as $file) {
                if (! $file->isFile() || $file->getExtension() !== 'php') {
                    continue;
                }

                $contents = file_get_contents($file->getPathname());
                self::assertIsString($contents);

                preg_match_all('/^use\\s+(App\\\\Tools\\\\[^;]+);/m', $contents, $matches);

                foreach ($matches[1] ?? [] as $import) {
                    self::assertStringStartsWith(
                        $ownNamespace,
                        $import,
                        "O arquivo [{$file->getPathname()}] importa uma classe interna de outra ferramenta: [{$import}].",
                    );
                }
            }
        }
    }

    /** @return array{string, int} */
    private function contractIdentity(string $contractKey): array
    {
        [$name, $version] = explode(':v', $contractKey, 2);

        return [$name, (int) $version];
    }
}
