<?php

namespace App\Providers;

use App\Console\Commands\CheckToolArchitectureCommand;
use App\Console\Commands\MakeToolCommand;
use App\Blog\Models\BlogPost;
use App\Console\Commands\PurgeExpiredToolRunsCommand;
use App\Core\Audit\Contracts\AuditLogger;
use App\Core\Audit\Services\DatabaseAuditLogger;
use App\Core\Imports\Contracts\ImportDatasetStore;
use App\Core\Imports\Infrastructure\CacheImportDatasetStore;
use App\Core\Imports\Services\CompositeTabularFileReader;
use App\Core\Imports\Services\CsvTabularFileReader;
use App\Core\Imports\Services\XlsxTabularFileReader;
use App\Core\Tools\History\Contracts\ToolRunRecorder;
use App\Core\Tools\History\Services\DatabaseToolRunRecorder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuditLogger::class, DatabaseAuditLogger::class);
        $this->app->bind(ToolRunRecorder::class, DatabaseToolRunRecorder::class);
        $this->app->bind(ImportDatasetStore::class, CacheImportDatasetStore::class);
        $this->app->singleton(CompositeTabularFileReader::class, static fn (): CompositeTabularFileReader => new CompositeTabularFileReader([
            new CsvTabularFileReader(),
            new XlsxTabularFileReader(),
        ]));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        View::composer('components.layout.right-sidebar', function ($view): void {
            $recentBlogPosts = Schema::hasTable('blog_posts')
                ? BlogPost::query()->publiclyAvailable()->take(3)->get()
                : collect();

            $view->with('recentBlogPosts', $recentBlogPosts);
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                CheckToolArchitectureCommand::class,
                MakeToolCommand::class,
                PurgeExpiredToolRunsCommand::class,
            ]);
        }
    }
}
