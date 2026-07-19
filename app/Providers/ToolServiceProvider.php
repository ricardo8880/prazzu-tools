<?php

namespace App\Providers;

use App\Core\Tools\Contracts\HasMigrations;
use App\Core\Tools\Contracts\HasServiceProviders;
use App\Core\Tools\Contracts\HasViews;
use App\Core\Tools\Contracts\ToolModule;
use App\Core\Tools\Support\ToolModuleValidator;
use App\Core\Tools\ToolCatalog;
use App\Core\Tools\ToolRegistry;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

final class ToolServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        /** @var array<int, class-string<ToolModule>> $modules */
        $modules = array_values(Arr::flatten(config('tools.modules', [])));
        $validator = $this->app->make(ToolModuleValidator::class);

        foreach ($modules as $moduleClass) {
            if (! is_string($moduleClass) || ! is_a($moduleClass, HasServiceProviders::class, true)) {
                continue;
            }

            /** @var HasServiceProviders&ToolModule $module */
            $module = $this->app->make($moduleClass);

            if (! $module instanceof ToolModule) {
                throw new InvalidArgumentException("O módulo [{$moduleClass}] deve implementar ".ToolModule::class.'.');
            }

            $validator->validate($module);

            foreach ($module->serviceProviders() as $providerClass) {
                $this->app->register($providerClass);
            }
        }

        $this->app->singleton(ToolRegistry::class, function ($app) use ($modules): ToolRegistry {
            return new ToolRegistry($app, $modules);
        });

        $this->app->singleton(ToolCatalog::class, fn ($app): ToolCatalog => new ToolCatalog(
            $app->make(ToolRegistry::class),
        ));
    }

    public function boot(ToolRegistry $registry, ToolCatalog $catalog): void
    {
        foreach ($registry->modules() as $module) {
            if ($module instanceof HasViews) {
                $this->loadViewsFrom($module->viewsPath(), $module->viewsNamespace());
            }

            if ($module instanceof HasMigrations) {
                $this->loadMigrationsFrom($module->migrationsPath());
            }
        }

        View::composer(['components.layout.left-sidebar', 'components.layout.mobile-navigation'], function ($view) use ($catalog): void {
            $view->with('toolCategories', $catalog->categories(false));
        });
    }
}
