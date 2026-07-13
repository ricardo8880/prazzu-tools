<?php

namespace App\Providers;

use App\Console\Commands\CheckToolArchitectureCommand;
use App\Console\Commands\MakeToolCommand;
use App\Console\Commands\PurgeExpiredToolRunsCommand;
use App\Core\Audit\Contracts\AuditLogger;
use App\Core\Audit\Services\DatabaseAuditLogger;
use App\Core\Tools\History\Contracts\ToolRunRecorder;
use App\Core\Tools\History\Services\DatabaseToolRunRecorder;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CheckToolArchitectureCommand::class,
                MakeToolCommand::class,
                PurgeExpiredToolRunsCommand::class,
            ]);
        }
    }
}
