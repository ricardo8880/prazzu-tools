<?php

declare(strict_types=1);

namespace App\Tools\NetSalaryCalculator\Presentation\Controllers;

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
use App\Tools\NetSalaryCalculator\Application\Actions\ManageToolHistory;
use App\Tools\NetSalaryCalculator\Application\Data\CalculationInput;
use App\Tools\NetSalaryCalculator\Domain\Services\Calculator;
use App\Tools\NetSalaryCalculator\Presentation\Requests\ExecuteToolRequest;
use App\Tools\NetSalaryCalculator\Tool;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class ToolController extends Controller
{
    public function index(): View
    {
        return view('tools-calculadora-salario-liquido::index');
    }

    public function calculate(ExecuteToolRequest $request, Calculator $calculator, ToolRunRecorder $recorder, ToolPersistenceAuthorizer $persistence, ToolFeatureRequestAuthorizer $features, Tool $module): View
    {
        $data = $request->validated();
        $features->requireIf($this->moneyPositive($data['taxable_additional_earnings'] ?? '0') || $this->moneyPositive($data['non_taxable_earnings'] ?? '0'), $module, 'variable_earnings', $request);
        $features->requireIf($this->moneyPositive($data['transport_discount'] ?? '0') || $this->moneyPositive($data['meal_discount'] ?? '0') || $this->moneyPositive($data['health_plan_discount'] ?? '0') || $this->moneyPositive($data['other_discounts'] ?? '0'), $module, 'custom_discounts', $request);
        $input = $this->input($data);
        $run = $this->startRun($request, $recorder, $persistence, $features, $module, $input);
        try {
            $result = $calculator->calculate($input);
            if ($run !== null) {
                $recorder->succeed($run, $result->toPersistenceArray());
            }
        } catch (Throwable $e) {
            if ($run !== null) {
                $recorder->fail($run, 'calculadora-salario-liquido.calculation_failed');
            } throw $e;
        }
        $request->flash();

        return view('tools-calculadora-salario-liquido::index', ['result' => $result, 'historySaved' => $run !== null]);
    }

    public function exportCurrent(ExecuteToolRequest $request, Calculator $calculator, ToolResultExportFactory $documents, PdfExporter $pdf, SpreadsheetExporter $spreadsheet, string $format): Response
    {
        abort_unless(in_array($format, ['pdf', 'xlsx'], true), 404);
        $input = $this->input($request->validated());
        $result = $calculator->calculate($input);
        $filename = 'salario-liquido-'.now()->format('Y-m-d');

        return $format === 'pdf' ? $pdf->download($documents->pdf('Relatório de Salário Líquido', $filename, $result, $input->toArray())) : $spreadsheet->download($documents->spreadsheet($filename, $result, $input->toArray()));
    }

    public function history(Request $request, ManageToolHistory $history): View
    {
        return view('tools-calculadora-salario-liquido::history.index', [
            'runs' => $history->paginate((int) $request->user()->getAuthIdentifier(), max(1, $request->integer('page', 1))),
        ]);
    }

    public function repeatHistory(Request $request, string $run, ManageToolHistory $history): RedirectResponse
    {
        $entry = $history->owned($run, (int) $request->user()->getAuthIdentifier());

        return redirect()->route('tools.calculadora-salario-liquido.index')->withInput([...$entry->input, 'confirm_assumptions' => 1])
            ->with('history_message', 'Dados recuperados. Revise as premissas antes de calcular novamente.');
    }

    public function destroyHistory(Request $request, string $run, ManageToolHistory $history): RedirectResponse
    {
        $history->delete($run, (int) $request->user()->getAuthIdentifier());

        return back()->with('history_message', 'Cálculo removido do histórico.');
    }

    private function input(array $data): CalculationInput
    {
        return new CalculationInput(
            competence: $data['competence'],
            baseSalary: $data['base_salary'],
            taxableAdditionalEarnings: $data['taxable_additional_earnings'] ?? '0',
            nonTaxableEarnings: $data['non_taxable_earnings'] ?? '0',
            dependents: (int) ($data['dependents'] ?? 0),
            judicialPension: $data['judicial_pension'] ?? '0',
            transportDiscount: $data['transport_discount'] ?? '0',
            mealDiscount: $data['meal_discount'] ?? '0',
            healthPlanDiscount: $data['health_plan_discount'] ?? '0',
            otherDiscounts: $data['other_discounts'] ?? '0',
        );
    }

    private function moneyPositive(mixed $value): bool
    {
        return Money::fromDecimal((string) ($value ?: '0'))->minorAmount() > 0;
    }

    private function startRun(
        Request $request,
        ToolRunRecorder $recorder,
        ToolPersistenceAuthorizer $persistence,
        ToolFeatureRequestAuthorizer $features,
        Tool $module,
        CalculationInput $input,
    ): ?ToolRunHandle {
        if (! $request->user() || ! $features->allows($module, 'history', $request) || ! $persistence->allowsHistory($module, $request->user())) {
            return null;
        }

        return $recorder->start(
            $module,
            new RuleVersion('2026.1.0'),
            ReferenceDate::fromString($input->competence.'-01'),
            $input->toArray(),
            $request->user()->id,
        );
    }
}
