<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Presentation\Controllers;

use App\Core\Access\Services\ToolPersistenceAuthorizer;
use App\Core\Dates\ReferenceDate;
use App\Core\Exceptions\InvalidValue;
use App\Core\Export\Data\PrintableDocument;
use App\Core\Export\Services\BrowserPrintExporter;
use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tools\History\Contracts\ToolRunRecorder;
use App\Core\Tools\History\Data\RuleVersion;
use App\Core\Tools\History\Data\ToolRunHandle;
use App\Core\Tools\Infrastructure\Contracts\ToolResultExporter;
use App\Http\Controllers\Controller;
use App\Tools\TaxRegimeComparator\Application\Actions\CompareTaxRegimes;
use App\Tools\TaxRegimeComparator\Application\Actions\DeleteTaxComparisonHistory;
use App\Tools\TaxRegimeComparator\Application\Actions\ListTaxComparisonHistory;
use App\Tools\TaxRegimeComparator\Application\Actions\RepeatTaxComparisonHistory;
use App\Tools\TaxRegimeComparator\Application\Actions\ShowTaxComparisonHistory;
use App\Tools\TaxRegimeComparator\Application\Actions\ShowToolPage;
use App\Tools\TaxRegimeComparator\Application\Data\TaxComparisonInput;
use App\Tools\TaxRegimeComparator\Application\Presenters\TaxComparisonResultPresenter;
use App\Tools\TaxRegimeComparator\Domain\Enums\BusinessActivity;
use App\Tools\TaxRegimeComparator\Presentation\Requests\CompareTaxRegimesRequest;
use App\Tools\TaxRegimeComparator\Tool;
use DateTimeImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Throwable;

final class ToolController extends Controller
{
    public function index(ShowToolPage $page): View
    {
        return view('tools-comparador-tributario::index', $page->execute());
    }

    public function compare(
        CompareTaxRegimesRequest $request,
        CompareTaxRegimes $compare,
        TaxComparisonResultPresenter $presenter,
        ToolRunRecorder $recorder,
        ToolPersistenceAuthorizer $persistence,
        Tool $module,
    ): RedirectResponse {
        $data = $request->validated();
        $run = $this->startRun($request, $recorder, $persistence, $module, $data);

        try {
            $result = $presenter->present($compare->execute($this->input($data)));
            if ($run !== null) {
                $recorder->succeed($run, $result);
            }
        } catch (InvalidValue $exception) {
            $this->failRun($recorder, $run, 'comparison.invalid_input');
            throw ValidationException::withMessages(['monthly_revenue' => $exception->getMessage()]);
        } catch (Throwable $exception) {
            $this->failRun($recorder, $run, 'comparison.failed');
            throw $exception;
        }

        return redirect()->route('tools.comparador-tributario.index')
            ->withInput()
            ->with('comparison_result', $result);
    }

    public function export(
        CompareTaxRegimesRequest $request,
        CompareTaxRegimes $compare,
        TaxComparisonResultPresenter $presenter,
        ToolResultExporter $exporter,
        Tool $module,
        string $format,
    ): Response {
        $format = strtolower($format);
        if (! in_array($format, ['csv', 'json'], true)) {
            abort(404);
        }

        try {
            $payload = $presenter->present($compare->execute($this->input($request->validated())));
        } catch (InvalidValue $exception) {
            throw ValidationException::withMessages(['monthly_revenue' => $exception->getMessage()]);
        }

        $content = $exporter->export($module, $payload, $format);
        $contentType = $format === 'json' ? 'application/json; charset=UTF-8' : 'text/csv; charset=UTF-8';

        return response($content, 200, [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'attachment; filename="comparacao-tributaria.'.$format.'"',
        ]);
    }

    public function report(
        CompareTaxRegimesRequest $request,
        CompareTaxRegimes $compare,
        TaxComparisonResultPresenter $presenter,
        BrowserPrintExporter $exporter,
    ): View {
        try {
            $result = $presenter->present($compare->execute($this->input($request->validated())));
        } catch (InvalidValue $exception) {
            throw ValidationException::withMessages(['monthly_revenue' => $exception->getMessage()]);
        }

        return $exporter->render(new PrintableDocument(
            title: 'Relatório de Comparação Tributária',
            subtitle: 'Simples Nacional, Lucro Presumido e Lucro Real',
            contentView: 'tools-comparador-tributario::pdf.report',
            data: ['result' => $result, 'input' => $request->validated()],
            generatedAt: now()->format('d/m/Y H:i'),
            summaryLabel: 'Menor ônus estimado',
            summaryValue: $result['winner'] ?? 'Sem comparação suficiente',
        ));
    }

    public function history(Request $request, ListTaxComparisonHistory $action): View
    {
        return view('tools-comparador-tributario::history.index', [
            'runs' => $action->execute(
                (int) $request->user()->getAuthIdentifier(),
                $request->filled('from') ? $request->string('from')->toString() : null,
                $request->filled('to') ? $request->string('to')->toString() : null,
                max(1, $request->integer('page', 1)),
            ),
        ]);
    }

    public function showHistory(Request $request, string $run, ShowTaxComparisonHistory $action): View
    {
        return view('tools-comparador-tributario::history.show', $action->execute(
            $run,
            (int) $request->user()->getAuthIdentifier(),
        ));
    }

    public function repeatHistory(Request $request, string $run, RepeatTaxComparisonHistory $action): RedirectResponse
    {
        return redirect()->route('tools.comparador-tributario.index')
            ->withInput($action->execute($run, (int) $request->user()->getAuthIdentifier()))
            ->with('history_message', 'Cenário carregado. Revise os dados antes de comparar novamente.');
    }

    public function destroyHistory(Request $request, string $run, DeleteTaxComparisonHistory $action): RedirectResponse
    {
        $action->execute($run, (int) $request->user()->getAuthIdentifier());

        return redirect()->route('tools.comparador-tributario.history.index')
            ->with('history_message', 'Comparação removida do histórico.');
    }

    /** @param array<string, mixed> $data */
    private function input(array $data): TaxComparisonInput
    {
        return new TaxComparisonInput(
            referenceDate: new DateTimeImmutable((string) $data['reference_date']),
            businessActivity: BusinessActivity::from((string) $data['business_activity']),
            monthlyRevenue: Money::fromDecimal((string) $data['monthly_revenue']),
            revenueLastTwelveMonths: Money::fromDecimal((string) $data['revenue_last_twelve_months']),
            payrollLastTwelveMonths: Money::fromDecimal((string) $data['payroll_last_twelve_months']),
            monthlyOperatingCosts: Money::fromDecimal((string) $data['monthly_operating_costs']),
            monthlyDeductibleExpenses: Money::fromDecimal((string) $data['monthly_deductible_expenses']),
            monthlyPisCofinsCreditBase: filled($data['monthly_pis_cofins_credit_base'] ?? null)
                ? Money::fromDecimal((string) $data['monthly_pis_cofins_credit_base']) : null,
            indirectTaxRate: filled($data['indirect_tax_rate'] ?? null)
                ? Percentage::fromString((string) $data['indirect_tax_rate']) : null,
            state: $data['state'] ?? null,
            municipality: $data['municipality'] ?? null,
        );
    }

    /** @param array<string, mixed> $data */
    private function startRun(Request $request, ToolRunRecorder $recorder, ToolPersistenceAuthorizer $persistence, Tool $module, array $data): ?ToolRunHandle
    {
        if (! $persistence->allowsHistory($module, $request->user())) {
            return null;
        }

        return $recorder->start(
            module: $module,
            ruleVersion: new RuleVersion('0.8.0'),
            referenceDate: ReferenceDate::fromString((string) $data['reference_date']),
            input: $data,
            userId: (int) $request->user()->getAuthIdentifier(),
        );
    }

    private function failRun(ToolRunRecorder $recorder, ?ToolRunHandle $run, string $code): void
    {
        if ($run !== null) {
            $recorder->fail($run, $code);
        }
    }
}
