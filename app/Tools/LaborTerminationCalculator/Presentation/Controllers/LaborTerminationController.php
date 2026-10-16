<?php

declare(strict_types=1);

namespace App\Tools\LaborTerminationCalculator\Presentation\Controllers;

use App\Core\Access\Services\ToolPersistenceAuthorizer;
use App\Core\Dates\ReferenceDate;
use App\Core\Exceptions\InvalidValue;
use App\Core\Export\Contracts\PdfExporter;
use App\Core\Export\Contracts\SpreadsheetExporter;
use App\Core\Export\Data\PdfDocument;
use App\Core\Export\Services\StructuredResultExportFactory;
use App\Core\Tools\History\Contracts\ToolRunRecorder;
use App\Core\Tools\History\Data\RuleVersion;
use App\Core\Tools\History\Data\ToolRunHandle;
use App\Http\Controllers\Controller;
use App\Tools\LaborTerminationCalculator\Application\Actions\CalculateLaborTermination;
use App\Tools\LaborTerminationCalculator\Application\Actions\ManageLaborTerminationHistory;
use App\Tools\LaborTerminationCalculator\Domain\Calculators\LaborTerminationCalculator;
use App\Tools\LaborTerminationCalculator\Presentation\Requests\CalculateLaborTerminationRequest;
use App\Tools\LaborTerminationCalculator\Tool;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class LaborTerminationController extends Controller
{
    public function index(Request $request, ManageLaborTerminationHistory $history): View
    {
        return view('tools-final-pay-severance-calculator::index', $this->pageData($request, $history));
    }

    public function calculate(
        CalculateLaborTerminationRequest $request,
        CalculateLaborTermination $action,
        ToolRunRecorder $recorder,
        Tool $module,
        ToolPersistenceAuthorizer $persistence,
        ManageLaborTerminationHistory $history,
    ): View {
        $input = $request->validated();
        $run = null;

        try {
            if ($persistence->allowsHistory($module, $request->user())) {
                $run = $recorder->start(
                    module: $module,
                    ruleVersion: new RuleVersion(LaborTerminationCalculator::RULE_VERSION),
                    referenceDate: ReferenceDate::fromString($input['termination_date']),
                    input: $input,
                    userId: $request->user()->id,
                );
            }

            $result = $action->execute($input);

            if ($run !== null) {
                $recorder->succeed($run, $result->toArray());
            }
        } catch (InvalidValue $exception) {
            $this->recordFailure($recorder, $run, 'calculation.invalid_input');
            throw ValidationException::withMessages(['hourly_rate' => $exception->getMessage()]);
        } catch (Throwable $exception) {
            $this->recordFailure($recorder, $run, 'calculation.failed');
            throw $exception;
        }

        $request->flash();

        return view('tools-final-pay-severance-calculator::index', $this->pageData($request, $history, [
            'calculationResult' => $result->toArray(),
            'calculationInput' => $input,
            'historySaved' => $run !== null,
        ]));
    }

    public function export(
        CalculateLaborTerminationRequest $request,
        CalculateLaborTermination $action,
        PdfExporter $exporter,
    ): Response {
        $input = $request->validated();

        Log::info('Labor termination PDF report requested.', [
            'tool' => Tool::SLUG,
            'separation_type' => $input['separation_type'] ?? null,
            'user_id' => $request->user()?->getAuthIdentifier(),
        ]);

        try {
            $result = $action->execute($input)->toArray();

            return $exporter->download(new PdfDocument(
                filename: 'final-pay-severance-'.now()->format('Y-m-d'),
                view: 'exports.printable-document',
                data: [
                    'title' => 'Final Pay & Severance Report',
                    'contentView' => 'tools-final-pay-severance-calculator::pdf.report',
                    'contentData' => ['result' => $result, 'input' => $input],
                    'generatedAt' => now()->format('d/m/Y H:i'),
                    'summaryLabel' => 'Estimated net final pay',
                    'summaryValue' => $result['net_total'] ?? null,
                ],
            ));
        } catch (InvalidValue $exception) {
            Log::warning('Labor termination PDF report validation failed.', [
                'tool' => Tool::SLUG,
                'message' => $exception->getMessage(),
            ]);
            throw ValidationException::withMessages(['hourly_rate' => $exception->getMessage()]);
        } catch (Throwable $exception) {
            Log::error('Labor termination PDF report failed.', [
                'tool' => Tool::SLUG,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);
            throw $exception;
        }
    }

    public function exportExcel(
        CalculateLaborTerminationRequest $request,
        CalculateLaborTermination $action,
        SpreadsheetExporter $exporter,
        StructuredResultExportFactory $documents,
    ): Response {
        $input = $request->validated();
        try {
            $result = $action->execute($input)->toArray();
        } catch (InvalidValue $exception) {
            throw ValidationException::withMessages(['hourly_rate' => $exception->getMessage()]);
        }

        return $exporter->download($documents->spreadsheet('final-pay-severance-'.now()->format('Y-m-d'), $result, $input));
    }

    public function exportHistory(
        Request $request,
        string $run,
        ManageLaborTerminationHistory $history,
    ): View {
        $run = $history->owned($run, (int) $request->user()->getAuthIdentifier());

        return view('exports.printable-document', [
            'title' => 'Final Pay & Severance Report',
            'contentView' => 'tools-final-pay-severance-calculator::pdf.report',
            'contentData' => ['result' => $run->result, 'input' => $run->input],
            'generatedAt' => $run->finishedAt->format('d/m/Y H:i'),
            'summaryLabel' => 'Estimated net final pay',
            'summaryValue' => $run->result['net_total'] ?? null,
        ]);
    }

    public function history(Request $request, ManageLaborTerminationHistory $history): View
    {
        return view('tools-final-pay-severance-calculator::history.index', [
            'runs' => $history->paginate((int) $request->user()->getAuthIdentifier(), page: max(1, $request->integer('page', 1))),
        ]);
    }

    public function showHistory(Request $request, string $run, ManageLaborTerminationHistory $history): View
    {
        $run = $history->owned($run, (int) $request->user()->getAuthIdentifier());

        return view('tools-final-pay-severance-calculator::history.show', ['run' => $run]);
    }

    public function repeatHistory(Request $request, string $run, ManageLaborTerminationHistory $history): RedirectResponse
    {
        $run = $history->owned($run, (int) $request->user()->getAuthIdentifier());

        return redirect()->route('tools.final-pay-severance-calculator.index')
            ->withInput($run->input)
            ->with('history_message', 'Saved inputs were restored. Review them before calculating again.');
    }

    public function destroyHistory(Request $request, string $run, ManageLaborTerminationHistory $history): RedirectResponse
    {
        $history->delete($run, (int) $request->user()->getAuthIdentifier());

        return redirect()->route('tools.final-pay-severance-calculator.history.index')
            ->with('history_message', 'Calculation removed from history.');
    }

    /**
     * @param array<string, mixed> $extra
     * @return array<string, mixed>
     */
    private function pageData(
        Request $request,
        ManageLaborTerminationHistory $history,
        array $extra = [],
    ): array {
        $recentHistory = $request->user() === null
            ? collect()
            : $history->recent((int) $request->user()->getAuthIdentifier());

        return array_merge([
            'separationTypes' => [
                'resignation' => 'Resignation',
                'layoff' => 'Layoff',
                'termination' => 'Employer termination',
                'contract_end' => 'Contract end',
                'other' => 'Other',
            ],
            'recentHistory' => $recentHistory,
            'calculationResult' => null,
            'calculationInput' => [],
            'historySaved' => false,
        ], $extra);
    }

    private function recordFailure(ToolRunRecorder $recorder, ?ToolRunHandle $run, string $errorCode): void
    {
        if ($run !== null) {
            $recorder->fail($run, $errorCode);
        }
    }
}
