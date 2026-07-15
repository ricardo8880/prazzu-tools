<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Core\Analytics\Application\Services\ScheduledAnalyticsReportRunner;
use App\Core\Analytics\Models\AnalyticsReportSchedule;
use Illuminate\Console\Command;

final class RunScheduledAnalyticsReportsCommand extends Command
{
    protected $signature = 'analytics:run-scheduled-reports';
    protected $description = 'Gera os relatórios de Analytics agendados que estão vencidos.';

    public function handle(ScheduledAnalyticsReportRunner $runner): int
    {
        AnalyticsReportSchedule::query()->where('is_active', true)->where('next_run_at', '<=', now())->orderBy('id')->eachById(function (AnalyticsReportSchedule $schedule) use ($runner): void {
            $runner->run($schedule);
            $this->line("Relatório processado: {$schedule->name}");
        });

        return self::SUCCESS;
    }
}
