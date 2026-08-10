<?php

namespace App\Providers;

use App\Blog\Models\BlogPost;
use App\Console\Commands\CheckToolArchitectureCommand;
use App\Console\Commands\MakeToolCommand;
use App\Console\Commands\PurgeExpiredToolRunsCommand;
use App\Core\Audit\Contracts\AuditLogger;
use App\Core\Audit\Services\DatabaseAuditLogger;
use App\Core\Export\Contracts\PdfExporter;
use App\Core\Export\Contracts\SpreadsheetExporter;
use App\Core\Export\Services\DompdfPdfExporter;
use App\Core\Export\Services\PhpSpreadsheetExporter;
use App\Core\Imports\Contracts\ImportDatasetStore;
use App\Core\Imports\Infrastructure\CacheImportDatasetStore;
use App\Core\Imports\Services\CompositeTabularFileReader;
use App\Core\Imports\Services\CsvTabularFileReader;
use App\Core\Imports\Services\XlsxTabularFileReader;
use App\Core\Organizations\Contracts\EnterpriseAccessResolver;
use App\Core\Organizations\Contracts\OrganizationSeatCounter;
use App\Core\Organizations\Services\DatabaseEnterpriseAccessResolver;
use App\Core\Organizations\Services\DatabaseOrganizationSeatCounter;
use App\Core\Temporary\Contracts\TemporaryPayloadStore;
use App\Core\Temporary\Infrastructure\CacheTemporaryPayloadStore;
use App\Core\Tools\Api\Auth\ApiClient;
use App\Core\Tools\Api\Http\Middleware\AuthenticateApiClient;
use App\Core\Tools\History\Contracts\ToolRunFavorites;
use App\Core\Tools\History\Contracts\ToolRunHistory;
use App\Core\Tools\History\Contracts\ToolRunRecorder;
use App\Core\Tools\History\Services\DatabaseToolRunFavorites;
use App\Core\Tools\History\Services\DatabaseToolRunHistory;
use App\Core\Tools\History\Services\DatabaseToolRunRecorder;
use App\Core\Tools\ToolRegistry;
use App\Core\Verticals\Application\VerticalContext;
use App\Core\Validation\BrazilianMoneyValidator;
use App\Core\Validation\BrazilianPercentageValidator;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuditLogger::class, DatabaseAuditLogger::class);
        $this->app->bind(PdfExporter::class, DompdfPdfExporter::class);
        $this->app->bind(SpreadsheetExporter::class, PhpSpreadsheetExporter::class);
        $this->app->bind(ToolRunRecorder::class, DatabaseToolRunRecorder::class);
        $this->app->bind(ToolRunHistory::class, DatabaseToolRunHistory::class);
        $this->app->bind(ToolRunFavorites::class, DatabaseToolRunFavorites::class);
        $this->app->bind(ImportDatasetStore::class, function ($app): CacheImportDatasetStore {
            $cache = config('e2e_environment.enabled')
                ? $app['cache']->build([
                    'driver' => 'file',
                    'path' => config('e2e_environment.paths.storage').'/import-dataset-cache',
                    'lock_path' => config('e2e_environment.paths.storage').'/import-dataset-cache',
                ])
                : $app['cache']->store();

            return new CacheImportDatasetStore($cache, $app->make(Encrypter::class));
        });
        $this->app->bind(TemporaryPayloadStore::class, function ($app): CacheTemporaryPayloadStore {
            $cache = config('e2e_environment.enabled')
                ? $app['cache']->build([
                    'driver' => 'file',
                    'path' => config('e2e_environment.paths.storage').'/temporary-payload-cache',
                    'lock_path' => config('e2e_environment.paths.storage').'/temporary-payload-cache',
                ])
                : $app['cache']->store();

            return new CacheTemporaryPayloadStore($cache);
        });
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

        $defaultVerticalSlug = config('verticals.default');
        $defaultPublicSlug = is_string($defaultVerticalSlug)
            ? config("verticals.registered.{$defaultVerticalSlug}.public_slug", $defaultVerticalSlug)
            : null;
        if (is_string($defaultPublicSlug) && $defaultPublicSlug !== '') {
            URL::defaults(['vertical' => $defaultPublicSlug]);
        }


        $moneyValidation = $this->app->make(BrazilianMoneyValidator::class);
        Validator::extend(
            'brazilian_money',
            static fn (string $attribute, mixed $value): bool => $moneyValidation->isValid($value),
            'O campo :attribute deve ser um valor monetário válido.',
        );
        Validator::extend(
            'money_min',
            static fn (string $attribute, mixed $value, array $parameters): bool => $moneyValidation->hasMinimum($value, $parameters),
            'O campo :attribute não atende ao valor mínimo permitido.',
        );

        $percentageValidation = $this->app->make(BrazilianPercentageValidator::class);
        Validator::extend(
            'brazilian_percentage',
            static fn (string $attribute, mixed $value): bool => $percentageValidation->isValid($value),
            'O campo :attribute deve ser um percentual válido com até seis casas decimais.',
        );
        Validator::extend(
            'percentage_min',
            static fn (string $attribute, mixed $value, array $parameters): bool => $percentageValidation->hasMinimum($value, $parameters),
            'O campo :attribute não atende ao percentual mínimo permitido.',
        );
        Validator::extend(
            'percentage_max',
            static fn (string $attribute, mixed $value, array $parameters): bool => $percentageValidation->hasMaximum($value, $parameters),
            'O campo :attribute excede o percentual máximo permitido.',
        );

        RateLimiter::for('tools-api', function (Request $request): Limit {
            $client = $request->attributes->get(AuthenticateApiClient::REQUEST_ATTRIBUTE);
            $key = $client instanceof ApiClient ? 'client:'.$client->id : 'ip:'.$request->ip();

            return Limit::perMinute(max(1, (int) config('tools-api.rate_limit', 120)))->by($key);
        });


        View::composer('layouts.app', function ($view): void {
            $routeName = request()->route()?->getName();
            $segments = is_string($routeName) ? explode('.', $routeName) : [];
            $analyticsToolSlug = ($segments[0] ?? null) === 'tools'
                && ! in_array($segments[1] ?? null, ['index', 'category', 'sitemap', 'suggest'], true)
                    ? ($segments[1] ?? null)
                    : null;

            $view->with('analyticsToolSlug', $analyticsToolSlug);
        });

        View::composer('components.layout.right-sidebar', function ($view): void {
            try {
                $recentBlogPosts = Schema::hasTable('blog_posts')
                    ? BlogPost::query()->forVertical(app(VerticalContext::class)->slug())->publiclyAvailable()->take(3)->get()
                    : collect();
            } catch (Throwable) {
                $recentBlogPosts = collect();
            }

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
