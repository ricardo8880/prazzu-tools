<?php

namespace App\Core\Tools;

use App\Core\Tools\Contracts\HasMigrations;
use App\Core\Tools\Contracts\ToolModule;
use App\Core\Tools\Data\ToolManifest;
use App\Core\Tools\Exceptions\DuplicateToolException;
use App\Core\Tools\Support\ToolModuleValidator;
use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;

final class ToolRegistry
{
    /** @var array<string, ToolModule> */
    private array $modules = [];

    /** @var array<string, string> */
    private array $migrationFiles = [];

    /** @param array<int, class-string<ToolModule>> $moduleClasses */
    public function __construct(
        Container $container,
        array $moduleClasses = [],
        private readonly ToolModuleValidator $validator = new ToolModuleValidator,
    ) {
        foreach ($moduleClasses as $moduleClass) {
            if (! is_string($moduleClass) || ! class_exists($moduleClass)) {
                throw new InvalidArgumentException('Todos os módulos configurados devem apontar para classes existentes.');
            }

            $module = $container->make($moduleClass);

            if (! $module instanceof ToolModule) {
                throw new InvalidArgumentException(sprintf(
                    'O módulo [%s] deve implementar %s.',
                    $moduleClass,
                    ToolModule::class,
                ));
            }

            $this->register($module);
        }
    }

    public function register(ToolModule $module): void
    {
        $this->validator->validate($module);

        $slug = $module->manifest()->slug;

        if (isset($this->modules[$slug])) {
            throw new DuplicateToolException("Já existe uma ferramenta registrada com o slug [{$slug}].");
        }

        $this->registerMigrationNames($module, $slug);
        $this->modules[$slug] = $module;
    }

    /** @return array<string, ToolModule> */
    public function modules(): array
    {
        return $this->modules;
    }

    /**
     * Compatibility alias for callers that enumerate registered modules.
     *
     * @return array<string, ToolModule>
     */
    public function all(): array
    {
        return $this->modules();
    }

    /** @return array<int, ToolManifest> */
    public function manifests(bool $onlyCatalogVisible = true): array
    {
        $manifests = array_map(
            static fn (ToolModule $module): ToolManifest => $module->manifest(),
            array_values($this->modules),
        );

        if ($onlyCatalogVisible) {
            $manifests = array_values(array_filter(
                $manifests,
                static fn (ToolManifest $tool): bool => $tool->status->isVisibleInCatalog(),
            ));
        }

        return $manifests;
    }

    public function findModule(string $slug): ?ToolModule
    {
        return $this->modules[$slug] ?? null;
    }

    public function findManifest(string $slug): ?ToolManifest
    {
        return $this->findModule($slug)?->manifest();
    }

    public function has(string $slug): bool
    {
        return isset($this->modules[$slug]);
    }

    public function count(): int
    {
        return count($this->modules);
    }

    private function registerMigrationNames(ToolModule $module, string $slug): void
    {
        if (! $module instanceof HasMigrations) {
            return;
        }

        $pending = [];

        foreach (glob(rtrim($module->migrationsPath(), '/\\').DIRECTORY_SEPARATOR.'*.php') ?: [] as $migration) {
            $name = strtolower(basename($migration));

            if (isset($this->migrationFiles[$name]) || isset($pending[$name])) {
                $owner = $this->migrationFiles[$name] ?? $slug;

                throw new InvalidArgumentException(
                    "A migration [{$name}] de [{$slug}] colide com a migration declarada por [{$owner}].",
                );
            }

            $pending[$name] = $slug;
        }

        foreach ($pending as $name => $owner) {
            $this->migrationFiles[$name] = $owner;
        }
    }
}
