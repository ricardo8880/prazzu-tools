<?php

declare(strict_types=1);

namespace App\Tools\LaborTerminationCalculator\Presentation\Controllers;

use App\Core\Access\Services\ToolPersistenceAuthorizer;
use App\Core\Dates\ReferenceDate;
use App\Core\Exceptions\InvalidValue;
use App\Core\Export\Data\PrintableDocument;
use App\Core\Export\Services\BrowserPrintExporter;
use App\Core\Tools\History\Contracts\ToolRunRecorder;
use App\Core\Tools\History\Data\RuleVersion;
use App\Core\Tools\History\Data\ToolRunHandle;
use App\Http\Controllers\Controller;
use App\Tools\LaborTerminationCalculator\Application\Actions\CalculateLaborTermination;
use App\Tools\LaborTerminationCalculator\Application\Actions\ManageLaborTerminationHistory;
use App\Tools\LaborTerminationCalculator\Domain\Calculators\LaborTerminationCalculator;
use App\Tools\LaborTerminationCalculator\Domain\Enums\NoticeType;
use App\Tools\LaborTerminationCalculator\Domain\Enums\TerminationType;
use App\Tools\LaborTerminationCalculator\Presentation\Requests\CalculateLaborTerminationRequest;
use App\Tools\LaborTerminationCalculator\Tool;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

final class LaborTerminationController extends Controller
{
    public function index(Request $request, ManageLaborTerminationHistory $history): View
    {
        $recentHistory = $request->user() === null
            ? collect()
            : $history->recent((int) $request->user()->getAuthIdentifier());

        return view('tools-calculadora-de-rescisao::index', [
            'terminationTypes' => $this->enumOptions(TerminationType::cases()),
            'contractTypes' => [
                'indefinite' => 'Prazo indeterminado',
                'fixed_term' => 'Prazo determinado',
                'experience' => 'Contrato de experiência',
                'domestic' => 'Empregado doméstico',
            ],
            'noticeTypes' => $this->enumOptions(NoticeType::cases()),
            'recentHistory' => $recentHistory,
        ]);
    }

    public function calculate(
        CalculateLaborTerminationRequest $request,
        CalculateLaborTermination $action,
        ToolRunRecorder $recorder,
        Tool $module,
        ToolPersistenceAuthorizer $persistence,
    ): RedirectResponse {
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
            throw ValidationException::withMessages(['notice_type' => $exception->getMessage()]);
        } catch (Throwable $exception) {
            $this->recordFailure($recorder, $run, 'calculation.failed');
            throw $exception;
        }

        return redirect()->route('tools.calculadora-de-rescisao.index')
            ->withInput()
            ->with('calculation_result', $result->toArray())
            ->with('calculation_input', $input)
            ->with('history_saved', $run !== null);
    }

    public function export(
        CalculateLaborTerminationRequest $request,
        CalculateLaborTermination $action,
        BrowserPrintExporter $exporter,
    ): View {
        $input = $request->validated();

        try {
            $result = $action->execute($input)->toArray();
        } catch (InvalidValue $exception) {
            throw ValidationException::withMessages(['notice_type' => $exception->getMessage()]);
        }

        return $this->pdfView($exporter, $result, $input, now()->format('d/m/Y H:i'));
    }

    public function exportHistory(
        Request $request,
        string $run,
        BrowserPrintExporter $exporter,
        ManageLaborTerminationHistory $history,
    ): View {
        $run = $history->owned($run, (int) $request->user()->getAuthIdentifier());

        return $this->pdfView(
            $exporter,
            $run->result,
            $run->input,
            $run->finishedAt->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i'),
        );
    }

    public function history(Request $request, ManageLaborTerminationHistory $history): View
    {
        return view('tools-calculadora-de-rescisao::history.index', [
            'runs' => $history->paginate((int) $request->user()->getAuthIdentifier(), page: max(1, $request->integer('page', 1))),
        ]);
    }

    public function showHistory(Request $request, string $run, ManageLaborTerminationHistory $history): View
    {
        $run = $history->owned($run, (int) $request->user()->getAuthIdentifier());

        return view('tools-calculadora-de-rescisao::history.show', ['run' => $run]);
    }

    public function repeatHistory(Request $request, string $run, ManageLaborTerminationHistory $history): RedirectResponse
    {
        $run = $history->owned($run, (int) $request->user()->getAuthIdentifier());

        return redirect()->route('tools.calculadora-de-rescisao.index')
            ->withInput($run->input)
            ->with('history_message', 'Os dados do cálculo foram carregados. Revise-os antes de calcular novamente.');
    }

    public function destroyHistory(Request $request, string $run, ManageLaborTerminationHistory $history): RedirectResponse
    {
        $history->delete($run, (int) $request->user()->getAuthIdentifier());

        return redirect()->route('tools.calculadora-de-rescisao.history.index')
            ->with('history_message', 'Cálculo removido do histórico.');
    }

    /** @param array<int, TerminationType|NoticeType> $cases
     *  @return array<string, string>
     */
    private function enumOptions(array $cases): array
    {
        $options = [];
        foreach ($cases as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }

    /**
     * @param array<string, mixed> $result
     * @param array<string, mixed> $input
     */
    private function pdfView(
        BrowserPrintExporter $exporter,
        array $result,
        array $input,
        string $generatedAt,
    ): View {
        return $exporter->render(new PrintableDocument(
            title: 'Relatório de Rescisão Trabalhista',
            subtitle: sprintf(
                '%s · %s',
                $result['termination_type_label'] ?? 'Rescisão trabalhista',
                $result['notice_type_label'] ?? 'Aviso não informado',
            ),
            contentView: 'tools-calculadora-de-rescisao::pdf.report',
            data: [
                'result' => $result,
                'input' => $input,
            ],
            generatedAt: $generatedAt,
            summaryLabel: 'Valor líquido estimado',
            summaryValue: $result['net_total'] ?? '—',
        ));
    }

    private function recordFailure(ToolRunRecorder $recorder, ?ToolRunHandle $run, string $errorCode): void
    {
        if ($run !== null) {
            $recorder->fail($run, $errorCode);
        }
    }
}
