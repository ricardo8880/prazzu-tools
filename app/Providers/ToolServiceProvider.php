<?php

namespace App\Providers;

use App\Core\Tools\Contracts\HasMigrations;
use App\Core\Tools\Contracts\HasViews;
use App\Core\Tools\ToolCatalog;
use App\Core\Tools\ToolRegistry;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

final class ToolServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ToolRegistry::class, function ($app): ToolRegistry {
            /** @var array<int, class-string<\App\Core\Tools\Contracts\ToolModule>> $modules */
            $modules = array_values(Arr::flatten(config('tools.modules', [])));

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

        View::composer('components.layout.right-sidebar', function ($view) use ($catalog): void {
            $view->with('popularTools', $catalog->popular());
        });
    }
}
