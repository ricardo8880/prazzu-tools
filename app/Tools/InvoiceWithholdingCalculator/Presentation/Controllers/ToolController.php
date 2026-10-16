<?php

declare(strict_types=1);

namespace App\Tools\InvoiceWithholdingCalculator\Presentation\Controllers;

use App\Core\Access\Services\ToolFeatureRequestAuthorizer;
use App\Core\Access\Services\ToolPersistenceAuthorizer;
use App\Core\Dates\ReferenceDate;
use App\Core\Export\Contracts\PdfExporter;
use App\Core\Export\Contracts\SpreadsheetExporter;
use App\Core\Export\Services\ToolResultExportFactory;
use App\Core\Money\Money;
use App\Core\Tools\History\Contracts\ToolRunRecorder;
use App\Core\Tools\History\Data\RuleVersion;
use App\Core\Tools\History\Data\ToolRunHandle;
use App\Http\Controllers\Controller;
use App\Tools\InvoiceWithholdingCalculator\Application\Actions\CalculateTool;
use App\Tools\InvoiceWithholdingCalculator\Application\Actions\ManageToolHistory;
use App\Tools\InvoiceWithholdingCalculator\Application\Actions\ShowToolPage;
use App\Tools\InvoiceWithholdingCalculator\Application\Data\CalculationInput;
use App\Tools\InvoiceWithholdingCalculator\Presentation\Requests\ExecuteToolRequest;
use App\Tools\InvoiceWithholdingCalculator\Tool;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class ToolController extends Controller
{
    public function index(Request $request, ShowToolPage $page, ToolFeatureRequestAuthorizer $features, Tool $module): View
    {
        return view('tools-backup-withholding-calculator::index', [...$page->execute(), 'plusEnabled' => $features->plusEnabled($module, $request)]);
    }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action, ToolRunRecorder $recorder, ToolPersistenceAuthorizer $persistence, ToolFeatureRequestAuthorizer $features, Tool $module, ShowToolPage $page): View
    {
        $data = $request->validated();
        $input = $this->input($data);
        $run = $this->startRun($request, $recorder, $persistence, $features, $module, $input);
        try {
            $result = $action->execute($input);
            if ($run !== null) {
                $recorder->succeed($run, $result->toPersistenceArray());
            }
        } catch (Throwable $e) {
            if ($run !== null) {
                $recorder->fail($run, 'backup-withholding-calculator.calculation_failed');
            } throw $e;
        }
        $request->flash();

        return view('tools-backup-withholding-calculator::index', [...$page->execute(), 'result' => $result, 'historySaved' => $run !== null, 'calculationInput' => $data, 'plusEnabled' => $features->plusEnabled($module, $request)]);
    }

    public function exportCurrent(ExecuteToolRequest $request, CalculateTool $action, ToolResultExportFactory $documents, PdfExporter $pdf, SpreadsheetExporter $spreadsheet, string $format): Response
    {
        abort_unless(in_array($format, ['pdf', 'xlsx'], true), 404);
        $input = $this->input($request->validated());
        $result = $action->execute($input);
        $filename = 'backup-withholding-'.$input->competence;

        return $format === 'pdf' ? $pdf->download($documents->pdf('Backup Withholding Calculation', $filename, $result, $input->toArray())) : $spreadsheet->download($documents->spreadsheet($filename, $result, $input->toArray()));
    }

    public function history(Request $request, ManageToolHistory $history): View
    {
        return view('tools-backup-withholding-calculator::history.index', [
            'runs' => $history->paginate((int) $request->user()->getAuthIdentifier(), max(1, $request->integer('page', 1))),
        ]);
    }

    public function repeatHistory(Request $request, string $run, ManageToolHistory $history): RedirectResponse
    {
        $entry = $history->owned($run, (int) $request->user()->getAuthIdentifier());

        return redirect()->route('tools.backup-withholding-calculator.index')->withInput([...$entry->input, 'confirm_scope' => 1])
            ->with('history_message', 'Saved inputs restored. Review them before calculating again.');
    }

    public function destroyHistory(Request $request, string $run, ManageToolHistory $history): RedirectResponse
    {
        $history->delete($run, (int) $request->user()->getAuthIdentifier());

        return back()->with('history_message', 'Calculation removed from history.');
    }

    private function input(array $d): CalculationInput
    {
        return new CalculationInput(
            competence: (string) $d['competence'],
            paymentReference: (string) ($d['payment_reference'] ?? ''),
            paymentDescription: (string) $d['payment_description'],
            grossValue: (string) $d['gross_value'],
            applyBackupWithholding: (bool) ($d['apply_backup_withholding'] ?? false),
            backupWithholdingRate: (string) ($d['backup_withholding_rate'] ?? '24'),
            stateWithholding: (string) ($d['state_withholding'] ?? '0'),
            otherWithholding: (string) ($d['other_withholding'] ?? '0'),
        );
    }

    private function startRun(Request $request, ToolRunRecorder $recorder, ToolPersistenceAuthorizer $persistence, ToolFeatureRequestAuthorizer $features, Tool $module, CalculationInput $input): ?ToolRunHandle
    {
        if (! $request->user() || ! $features->allows($module, 'history', $request) || ! $persistence->allowsHistory($module, $request->user())) {
            return null;
        }

        return $recorder->start($module, new RuleVersion('2026.1.0'), ReferenceDate::fromString($input->competence.'-01'), $input->toArray(), $request->user()->id);
    }
}
