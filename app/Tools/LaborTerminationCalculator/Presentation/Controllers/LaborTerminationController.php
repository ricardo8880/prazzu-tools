<?php

declare(strict_types=1);

namespace App\Tools\LaborTerminationCalculator\Presentation\Controllers;

use App\Core\Audit\Contracts\AuditLogger;
use App\Core\Dates\ReferenceDate;
use App\Core\Exceptions\InvalidValue;
use App\Core\Export\Data\PrintableDocument;
use App\Core\Export\Services\BrowserPrintExporter;
use App\Core\Tools\History\Contracts\ToolRunRecorder;
use App\Core\Tools\History\Data\RuleVersion;
use App\Core\Tools\History\Enums\ToolRunStatus;
use App\Core\Tools\History\Models\ToolRun;
use App\Http\Controllers\Controller;
use App\Tools\LaborTerminationCalculator\Application\Actions\CalculateLaborTermination;
use App\Tools\LaborTerminationCalculator\Domain\Calculators\LaborTerminationCalculator;
use App\Tools\LaborTerminationCalculator\Domain\Enums\NoticeType;
use App\Tools\LaborTerminationCalculator\Domain\Enums\TerminationType;
use App\Tools\LaborTerminationCalculator\Presentation\Requests\CalculateLaborTerminationRequest;
use App\Tools\LaborTerminationCalculator\Tool;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;
use Throwable;

final class LaborTerminationController extends Controller
{
    public function index(Request $request): View
    {
        $recentHistory = $request->user() === null
            ? collect()
            : $this->historyQuery($request)->limit(3)->get();

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
    ): RedirectResponse {
        $input = $request->validated();
        $run = null;

        try {
            if ($request->user() !== null) {
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

    public function exportHistory(Request $request, ToolRun $run, BrowserPrintExporter $exporter): View
    {
        $run = $this->ownedRun($request, $run);

        return $this->pdfView(
            $exporter,
            $run->result_payload ?? [],
            $run->input_payload ?? [],
            $run->finished_at?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i'),
        );
    }

    public function history(Request $request): View
    {
        return view('tools-calculadora-de-rescisao::history.index', [
            'runs' => $this->historyQuery($request)->paginate(10),
        ]);
    }

    public function showHistory(Request $request, ToolRun $run): View
    {
        $run = $this->ownedRun($request, $run);

        return view('tools-calculadora-de-rescisao::history.show', ['run' => $run]);
    }

    public function repeatHistory(Request $request, ToolRun $run): RedirectResponse
    {
        $run = $this->ownedRun($request, $run);

        return redirect()->route('tools.calculadora-de-rescisao.index')
            ->withInput($run->input_payload ?? [])
            ->with('history_message', 'Os dados do cálculo foram carregados. Revise-os antes de calcular novamente.');
    }

    public function destroyHistory(Request $request, ToolRun $run, AuditLogger $audit): RedirectResponse
    {
        $run = $this->ownedRun($request, $run);
        $runId = $run->id;

        $audit->record(
            action: 'tool_run.deleted',
            auditableType: ToolRun::class,
            auditableId: $runId,
            metadata: ['tool_slug' => $run->tool_slug],
            actorId: $request->user()->id,
        );

        $run->delete();

        return redirect()->route('tools.calculadora-de-rescisao.history.index')
            ->with('history_message', 'Cálculo removido do histórico.');
    }

    private function historyQuery(Request $request)
    {
        return ToolRun::query()
            ->where('user_id', $request->user()->id)
            ->where('tool_slug', 'calculadora-de-rescisao')
            ->where('status', ToolRunStatus::Succeeded)
            ->latest('finished_at');
    }

    private function ownedRun(Request $request, ToolRun $run): ToolRun
    {
        abort_unless(
            $run->user_id === $request->user()->id
            && $run->tool_slug === 'calculadora-de-rescisao'
            && $run->status === ToolRunStatus::Succeeded,
            404,
        );

        return $run;
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

    private function recordFailure(ToolRunRecorder $recorder, ?ToolRun $run, string $errorCode): void
    {
        if ($run !== null) {
            $recorder->fail($run, $errorCode);
        }
    }
}
