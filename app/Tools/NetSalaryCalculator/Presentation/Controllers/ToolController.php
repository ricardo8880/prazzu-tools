<?php

declare(strict_types=1);

namespace App\Tools\NetSalaryCalculator\Presentation\Controllers;

use App\Core\Access\Services\ToolPersistenceAuthorizer;
use App\Core\Dates\ReferenceDate;
use App\Core\Export\Data\PrintableDocument;
use App\Core\Export\Services\BrowserPrintExporter;
use App\Core\Export\Services\TabularExportService;
use App\Core\Tools\History\Contracts\ToolRunRecorder;
use App\Core\Tools\History\Data\RuleVersion;
use App\Core\Tools\History\Data\ToolRunHandle;
use App\Http\Controllers\Controller;
use App\Tools\NetSalaryCalculator\Application\Data\CalculationInput;
use App\Tools\NetSalaryCalculator\Domain\Services\Calculator;
use App\Tools\NetSalaryCalculator\Presentation\Requests\ExecuteToolRequest;
use App\Tools\NetSalaryCalculator\Tool;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

final class ToolController extends Controller
{
    public function index(): View
    {
        return view('tools-calculadora-salario-liquido::index');
    }

    public function calculate(
        ExecuteToolRequest $request,
        Calculator $calculator,
        ToolRunRecorder $recorder,
        ToolPersistenceAuthorizer $persistence,
        Tool $module,
    ): View {
        $data = $request->validated();
        $input = $this->input($data);
        $run = $this->startRun($request, $recorder, $persistence, $module, $input);

        try {
            $result = $calculator->calculate($input);
            if ($run !== null) {
                $recorder->succeed($run, $result->toPersistenceArray());
            }
        } catch (Throwable $exception) {
            if ($run !== null) {
                $recorder->fail($run, 'net_salary.calculation_failed');
            }
            throw $exception;
        }

        $request->flash();

        return view('tools-calculadora-salario-liquido::index', [
            'result' => $result,
            'historySaved' => $run !== null,
        ]);
    }

    public function printCurrent(ExecuteToolRequest $request, Calculator $calculator, BrowserPrintExporter $print): View
    {
        $data = $request->validated();
        $result = $calculator->calculate($this->input($data));

        return $print->render(new PrintableDocument(
            title: 'Relatório de Salário Líquido',
            contentView: 'tools-calculadora-salario-liquido::report',
            data: ['result' => $result],
            subtitle: 'Estimativa mensal CLT — competência '.$data['competence'],
            generatedAt: now()->format('d/m/Y H:i'),
            summaryLabel: 'Salário líquido',
            summaryValue: $result->summary[4]->value,
        ));
    }

    public function exportCurrent(ExecuteToolRequest $request, Calculator $calculator, TabularExportService $export): Response|StreamedResponse
    {
        $result = $calculator->calculate($this->input($request->validated()));
        $rows = array_map(static fn ($item): array => [$item->label, $item->value], $result->summary);

        return $export->csv('salario-liquido-'.now()->format('Y-m-d').'.csv', ['Campo', 'Valor'], $rows);
    }

    /** @param array<string, mixed> $data */
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

    private function startRun(
        Request $request,
        ToolRunRecorder $recorder,
        ToolPersistenceAuthorizer $persistence,
        Tool $module,
        CalculationInput $input,
    ): ?ToolRunHandle {
        if (! $request->user() || ! $persistence->allowsHistory($module, $request->user())) {
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
