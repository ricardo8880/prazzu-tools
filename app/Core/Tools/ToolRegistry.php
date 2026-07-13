<?php

namespace App\Core\Tools;

use App\Core\Tools\Contracts\ToolModule;
use App\Core\Tools\Data\ToolDefinition;
use App\Core\Tools\Exceptions\DuplicateToolException;
use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;

final class ToolRegistry
{
    /** @var array<string, ToolModule> */
    private array $modules = [];

    /**
     * @param array<int, class-string<ToolModule>> $moduleClasses
     */
    public function __construct(Container $container, array $moduleClasses = [])
    {
        foreach ($moduleClasses as $moduleClass) {
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
        $slug = $module->definition()->slug;

        if (isset($this->modules[$slug])) {
            throw new DuplicateToolException("Já existe uma ferramenta registrada com o slug [{$slug}].");
        }

        $this->modules[$slug] = $module;
    }

    /** @return array<string, ToolModule> */
    public function modules(): array
    {
        return $this->modules;
    }

    /** @return array<int, ToolDefinition> */
    public function definitions(bool $onlyActive = true): array
    {
        $definitions = array_map(
            static fn (ToolModule $module): ToolDefinition => $module->definition(),
            array_values($this->modules),
        );

        if ($onlyActive) {
            $definitions = array_values(array_filter(
                $definitions,
                static fn (ToolDefinition $tool): bool => $tool->isActive,
            ));
        }

        return $definitions;
    }

    public function find(string $slug): ?ToolDefinition
    {
        $module = $this->modules[$slug] ?? null;

        return $module?->definition();
    }
}
