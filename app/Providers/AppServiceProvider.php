<?php

namespace App\Providers;

use App\Blog\Models\BlogPost;
use App\Console\Commands\CheckToolArchitectureCommand;
use App\Console\Commands\MakeToolCommand;
use App\Console\Commands\PurgeExpiredToolRunsCommand;
use App\Core\Audit\Contracts\AuditLogger;
use App\Core\Audit\Services\DatabaseAuditLogger;
use App\Core\Imports\Contracts\ImportDatasetStore;
use App\Core\Imports\Infrastructure\CacheImportDatasetStore;
use App\Core\Imports\Services\CompositeTabularFileReader;
use App\Core\Imports\Services\CsvTabularFileReader;
use App\Core\Imports\Services\XlsxTabularFileReader;
use App\Core\Temporary\Contracts\TemporaryPayloadStore;
use App\Core\Temporary\Infrastructure\CacheTemporaryPayloadStore;
use App\Core\Organizations\Contracts\EnterpriseAccessResolver;
use App\Core\Organizations\Contracts\OrganizationSeatCounter;
use App\Core\Organizations\Services\DatabaseEnterpriseAccessResolver;
use App\Core\Organizations\Services\DatabaseOrganizationSeatCounter;
use App\Core\Tools\History\Contracts\ToolRunFavorites;
use App\Core\Tools\History\Contracts\ToolRunHistory;
use App\Core\Tools\History\Contracts\ToolRunRecorder;
use App\Core\Tools\History\Services\DatabaseToolRunFavorites;
use App\Core\Tools\History\Services\DatabaseToolRunHistory;
use App\Core\Tools\Api\Auth\ApiClient;
use App\Core\Tools\Api\Http\Middleware\AuthenticateApiClient;
use App\Core\Tools\History\Services\DatabaseToolRunRecorder;
use App\Core\Tools\ToolRegistry;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
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
        $this->app->bind(ToolRunHistory::class, DatabaseToolRunHistory::class);
        $this->app->bind(ToolRunFavorites::class, DatabaseToolRunFavorites::class);
        $this->app->bind(ImportDatasetStore::class, CacheImportDatasetStore::class);
        $this->app->bind(TemporaryPayloadStore::class, CacheTemporaryPayloadStore::class);
        $this->app->bind(EnterpriseAccessResolver::class, DatabaseEnterpriseAccessResolver::class);
        $this->app->bind(OrganizationSeatCounter::class, DatabaseOrganizationSeatCounter::class);
        $this->app->singleton(CompositeTabularFileReader::class, static fn (): CompositeTabularFileReader => new CompositeTabularFileReader([
            new CsvTabularFileReader,
            new XlsxTabularFileReader,
        ]));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        RateLimiter::for('tools-api', function (Request $request): Limit {
            $client = $request->attributes->get(AuthenticateApiClient::REQUEST_ATTRIBUTE);
            $key = $client instanceof ApiClient ? 'client:'.$client->id : 'ip:'.$request->ip();

            return Limit::perMinute(max(1, (int) config('tools-api.rate_limit', 120)))->by($key);
        });

        View::composer('components.layout.right-sidebar', function ($view): void {
            $recentBlogPosts = Schema::hasTable('blog_posts')
                ? BlogPost::query()->publiclyAvailable()->take(3)->get()
                : collect();

            $routeName = request()->route()?->getName();
            $segments = is_string($routeName) ? explode('.', $routeName) : [];
            $toolSlug = ($segments[0] ?? null) === 'tools' ? ($segments[1] ?? null) : null;
            $toolFeedbackManifest = is_string($toolSlug)
                ? app(ToolRegistry::class)->findManifest($toolSlug)
                : null;

            $view->with([
                'recentBlogPosts' => $recentBlogPosts,
                'toolFeedbackManifest' => $toolFeedbackManifest,
            ]);
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
