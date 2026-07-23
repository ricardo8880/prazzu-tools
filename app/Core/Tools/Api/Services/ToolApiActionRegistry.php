<?php

namespace App\Core\Tools\Api\Services;

use App\Core\Tools\Api\Contracts\HasApiActions;
use App\Core\Tools\Api\Contracts\ToolApiAction;
use App\Core\Tools\Api\Exceptions\InvalidToolApiAction;
use App\Core\Tools\Api\Exceptions\ToolApiActionNotFound;
use App\Core\Tools\ToolRegistry;
use Illuminate\Contracts\Container\Container;

final class ToolApiActionRegistry
{
    /** @var array<string, array<string, class-string<ToolApiAction>>>|null */
    private ?array $actions = null;

    public function __construct(
        private readonly Container $container,
        private readonly ToolRegistry $tools,
    ) {}

    public function resolve(string $tool, string $action): ToolApiAction
    {
        $class = $this->all()[$tool][$action] ?? null;

        if ($class === null) {
            throw ToolApiActionNotFound::for($tool, $action);
        }

        return $this->container->make($class);
    }

    /** @return array<string, array<string, class-string<ToolApiAction>>> */
    public function all(): array
    {
        return $this->actions ??= $this->discover();
    }

    /** @return array<string, array<string, class-string<ToolApiAction>>> */
    private function discover(): array
    {
        $registered = [];

        foreach ($this->tools->modules() as $tool => $module) {
            if (! $module instanceof HasApiActions) {
                continue;
            }

            foreach ($module->apiActions() as $actionClass) {
                $action = $this->container->make($actionClass);
                $name = trim($action->name());

                if ($name === '' || preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $name) !== 1) {
                    throw InvalidToolApiAction::name($actionClass);
                }

                if (isset($registered[$tool][$name])) {
                    throw InvalidToolApiAction::duplicate($tool, $name);
                }

                $registered[$tool][$name] = $actionClass;
            }
        }

        return $registered;
    }
}
