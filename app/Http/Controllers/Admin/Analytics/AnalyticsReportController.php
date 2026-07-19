<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Analytics;

use App\Core\Analytics\Application\Queries\AnalyticsReportQuery;
use App\Core\Analytics\Application\Services\AnalyticsReportFileBuilder;
use App\Core\Analytics\Application\Services\ScheduledAnalyticsReportRunner;
use App\Core\Analytics\Application\Services\StrategicAnalyticsPackageBuilder;
use App\Core\Analytics\Application\Services\StrategicAnalyticsReportBuilder;
use App\Core\Analytics\Models\AnalyticsReportSchedule;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Analytics\AnalyticsReportRequest;
use App\Http\Requests\Admin\Analytics\StoreAnalyticsReportScheduleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class AnalyticsReportController extends Controller
{
    public function index(AnalyticsReportRequest $request, AnalyticsReportQuery $query): View
    {
        return view('admin.analytics.reports', $query->execute($request->period(), $request->filters()) + [
            'selected_period' => $request->validated('period', '30'),
            'filters' => $request->filters(),
            'schedules' => AnalyticsReportSchedule::query()->latest()->get(),
        ]);
    }

    public function export(AnalyticsReportRequest $request, AnalyticsReportQuery $query, AnalyticsReportFileBuilder $files, StrategicAnalyticsReportBuilder $strategic, StrategicAnalyticsPackageBuilder $packages): Response|StreamedResponse
    {
        $format = (string) $request->validated('format', 'csv');
        if (in_array($format, ['markdown', 'json', 'package', 'package_summary'], true)) {
            $payload = $strategic->payload($request->period(), $request->filters(), $query->strategic($request->period(), $request->filters()));
            if (in_array($format, ['package', 'package_summary'], true)) {
                $rows = $query->rows($request->period(), $request->filters(), (int) config('analytics.reports.export_limit', 10000));
                $content = $packages->build($payload, $rows, $format === 'package_summary');
            } else {
                $content = $format === 'json' ? $strategic->json($payload) : $strategic->markdown($payload);
            }
        } else {
            $rows = $query->rows($request->period(), $request->filters(), (int) config('analytics.reports.export_limit', 10000));
            $content = $files->build($format, $rows, 'Relatório Analytics - '.$request->period()->label());
        }
        $extension = match ($format) { 'excel' => 'xml', 'markdown' => 'md', 'package', 'package_summary' => 'zip', default => $format };
        $mime = match ($format) {
            'excel' => 'application/vnd.ms-excel; charset=UTF-8',
            'pdf' => 'application/pdf',
            'json' => 'application/json; charset=UTF-8',
            'markdown' => 'text/markdown; charset=UTF-8',
            'package', 'package_summary' => 'application/zip',
            default => 'text/csv; charset=UTF-8',
        };

        return response($content, 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'attachment; filename="analytics-'.$request->period()->start->format('Ymd').'-'.$request->period()->end->format('Ymd').'.'.$extension.'"',
        ]);
    }

    public function storeSchedule(StoreAnalyticsReportScheduleRequest $request, ScheduledAnalyticsReportRunner $runner): RedirectResponse
    {
        $data = $request->validated();
        $filters = collect($data)->except(['name', 'frequency', 'format'])->filter(fn ($value) => $value !== null && $value !== '')->all();
        AnalyticsReportSchedule::query()->create([
            'name' => $data['name'], 'frequency' => $data['frequency'], 'format' => $data['format'],
            'filters' => $filters, 'is_active' => true, 'next_run_at' => $runner->nextRun($data['frequency']),
        ]);

        return back()->with('status', 'Relatório agendado com sucesso.');
    }

    public function toggleSchedule(AnalyticsReportSchedule $schedule): RedirectResponse
    {
        $schedule->update(['is_active' => ! $schedule->is_active]);

        return back()->with('status', $schedule->is_active ? 'Agendamento ativado.' : 'Agendamento pausado.');
    }

    public function destroySchedule(AnalyticsReportSchedule $schedule): RedirectResponse
    {
        $schedule->delete();

        return back()->with('status', 'Agendamento removido.');
    }

    public function downloadSchedule(AnalyticsReportSchedule $schedule): StreamedResponse
    {
        abort_unless($schedule->last_file_path && Storage::disk('local')->exists($schedule->last_file_path), 404);

        return Storage::disk('local')->download($schedule->last_file_path);
    }
}
