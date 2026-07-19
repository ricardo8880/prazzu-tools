<?php

declare(strict_types=1);

namespace App\Core\Analytics\Application\Services;

use App\Core\Analytics\Application\Queries\AnalyticsReportQuery;
use App\Core\Analytics\Domain\ValueObjects\AnalyticsPeriod;
use App\Core\Analytics\Models\AnalyticsReportSchedule;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Storage;
use Throwable;

final class ScheduledAnalyticsReportRunner
{
    public function __construct(
        private readonly AnalyticsReportQuery $query,
        private readonly AnalyticsReportFileBuilder $files,
    ) {}

    public function run(AnalyticsReportSchedule $schedule): void
    {
        try {
            $filters = (array) ($schedule->filters ?? []);
            $period = AnalyticsPeriod::lastDays((int) ($filters['period'] ?? 30));
            unset($filters['period']);
            $rows = $this->query->rows($period, $filters, (int) config('analytics.reports.export_limit', 10000));
            $extension = $schedule->format === 'excel' ? 'xml' : $schedule->format;
            $path = 'analytics-reports/'.now()->format('Y/m').'/'.str($schedule->name)->slug().'-'.now()->format('Ymd-His').'.'.$extension;
            Storage::disk('local')->put($path, $this->files->build($schedule->format, $rows, $schedule->name));

            $schedule->forceFill([
                'last_run_at' => now(), 'last_file_path' => $path, 'last_error' => null,
                'next_run_at' => $this->nextRun($schedule->frequency),
            ])->save();
        } catch (Throwable $exception) {
            report($exception);
            $schedule->forceFill(['last_run_at' => now(), 'last_error' => mb_substr($exception->getMessage(), 0, 1000), 'next_run_at' => $this->nextRun($schedule->frequency)])->save();
        }
    }

    public function nextRun(string $frequency): CarbonImmutable
    {
        $now = now()->toImmutable();

        return match ($frequency) {
            'weekly' => $now->addWeek()->startOfDay()->setTime(7, 0),
            'monthly' => $now->addMonthNoOverflow()->startOfDay()->setTime(7, 0),
            default => $now->addDay()->startOfDay()->setTime(7, 0),
        };
    }
}
