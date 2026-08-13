<?php

declare(strict_types=1);

namespace App\Tools\PresumedProfitIrpjCsllCalculator\Presentation\Controllers;

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
use App\Tools\PresumedProfitIrpjCsllCalculator\Application\Actions\CalculateTool;
use App\Tools\PresumedProfitIrpjCsllCalculator\Application\Actions\ManageToolHistory;
use App\Tools\PresumedProfitIrpjCsllCalculator\Application\Actions\ShowToolPage;
use App\Tools\PresumedProfitIrpjCsllCalculator\Application\Data\CalculationInput;
use App\Tools\PresumedProfitIrpjCsllCalculator\Presentation\Requests\ExecuteToolRequest;
use App\Tools\PresumedProfitIrpjCsllCalculator\Tool;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class ToolController extends Controller
{
    public function index(Request $request, ShowToolPage $page, ToolFeatureRequestAuthorizer $features, Tool $module): View
    {
        return view('tools-calculadora-irpj-csll-lucro-presumido::index', [...$page->execute(), 'plusEnabled' => $features->plusEnabled($module, $request)]);
    }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action, ToolRunRecorder $recorder, ToolPersistenceAuthorizer $persistence, ToolFeatureRequestAuthorizer $features, Tool $module, ShowToolPage $page): View
    {
        $data = $request->validated();
        $features->requireIf(($data['periodicity'] ?? 'quarterly') === 'monthly', $module, 'periodicity', $request);
        $activityCount = 0;
        foreach (['commerce_revenue', 'fuel_revenue', 'passenger_transport_revenue', 'services_revenue'] as $field) {
            if ($this->moneyPositive($data[$field] ?? '0')) {
                $activityCount++;
            }
        }
        $features->requireIf($activityCount > 1, $module, 'multiple_activities', $request);
        $features->requireIf(count($data['scenarios'] ?? []) > 0, $module, 'scenario_comparison', $request);
        $features->requireIf($this->moneyPositive($data['prior_irpj_presumption_revenue'] ?? '0') || $this->moneyPositive($data['prior_csll_presumption_revenue'] ?? '0'), $module, 'carry_forward_limit', $request);
        $features->requireIf($this->moneyPositive($data['irpj_credits'] ?? '0') || $this->moneyPositive($data['csll_credits'] ?? '0'), $module, 'credits', $request);
        $input = $this->input($data);
        $run = $this->startRun($request, $recorder, $persistence, $features, $module, $input);
        try {
            $result = $action->execute($input);
            if ($run !== null) {
                $recorder->succeed($run, $result->toPersistenceArray());
            }
        } catch (Throwable $e) {
            if ($run !== null) {
                $recorder->fail($run, 'calculadora-irpj-csll-lucro-presumido.calculation_failed');
            }
            throw $e;
        }
        $request->flash();

        return view('tools-calculadora-irpj-csll-lucro-presumido::index', [
            ...$page->execute(), 'result' => $result, 'historySaved' => $run !== null, 'calculationInput' => $data, 'plusEnabled' => $features->plusEnabled($module, $request),
        ]);
    }

    public function exportCurrent(ExecuteToolRequest $request, CalculateTool $action, ToolResultExportFactory $documents, PdfExporter $pdf, SpreadsheetExporter $spreadsheet, string $format): Response
    {
        abort_unless(in_array($format, ['pdf', 'xlsx'], true), 404);
        $input = $this->input($request->validated());
        $result = $action->execute($input);
        $filename = 'irpj-csll-lucro-presumido-'.now()->format('Y-m-d');

        return $format === 'pdf'
            ? $pdf->download($documents->pdf('IRPJ e CSLL — Lucro Presumido', $filename, $result, $input->toArray()))
            : $spreadsheet->download($documents->spreadsheet($filename, $result, $input->toArray()));
    }

    public function history(Request $request, ManageToolHistory $history): View
    {
        return view('tools-calculadora-irpj-csll-lucro-presumido::history.index', [
            'runs' => $history->paginate((int) $request->user()->getAuthIdentifier(), max(1, $request->integer('page', 1))),
        ]);
    }

    public function repeatHistory(Request $request, string $run, ManageToolHistory $history): RedirectResponse
    {
        $entry = $history->owned($run, (int) $request->user()->getAuthIdentifier());

        return redirect()->route('tools.calculadora-irpj-csll-lucro-presumido.index')->withInput([...$entry->input, 'confirm_scope' => 1])
            ->with('history_message', 'Dados recuperados. Revise as premissas antes de calcular novamente.');
    }

    public function destroyHistory(Request $request, string $run, ManageToolHistory $history): RedirectResponse
    {
        $history->delete($run, (int) $request->user()->getAuthIdentifier());

        return back()->with('history_message', 'Cálculo removido do histórico.');
    }

    private function input(array $d): CalculationInput
    {
        $scenarios = [];
        foreach (($d['scenarios'] ?? []) as $index => $scenario) {
            $hasRevenue = false;
            foreach (['commerce_revenue', 'fuel_revenue', 'passenger_transport_revenue', 'services_revenue'] as $field) {
                $value = trim((string) ($scenario[$field] ?? ''));
                if ($value !== '' && $value !== '0' && $value !== '0,00') {
                    $hasRevenue = true;
                    break;
                }
            }
            if (! $hasRevenue) {
                continue;
            }
            $scenarios[] = [
                'name' => trim((string) ($scenario['name'] ?? '')) ?: 'Cenário '.($index + 2),
                'commerce_revenue' => (string) ($scenario['commerce_revenue'] ?? '0'),
                'fuel_revenue' => (string) ($scenario['fuel_revenue'] ?? '0'),
                'passenger_transport_revenue' => (string) ($scenario['passenger_transport_revenue'] ?? '0'),
                'services_revenue' => (string) ($scenario['services_revenue'] ?? '0'),
                'other_taxable_additions' => (string) ($scenario['other_taxable_additions'] ?? '0'),
            ];
        }

        $periodicity = (string) ($d['periodicity'] ?? 'quarterly');
        $month = isset($d['month']) && $d['month'] !== '' ? (int) $d['month'] : null;
        $quarter = $periodicity === 'monthly' && $month !== null ? (int) ceil($month / 3) : (int) ($d['quarter'] ?? 1);

        return new CalculationInput(
            $quarter, (string) $d['commerce_revenue'], (string) $d['fuel_revenue'],
            (string) $d['passenger_transport_revenue'], (string) $d['services_revenue'],
            (string) $d['other_taxable_additions'], (string) $d['prior_irpj_presumption_revenue'],
            (string) $d['prior_csll_presumption_revenue'], (string) $d['irpj_credits'], (string) $d['csll_credits'],
            $periodicity, $month, $scenarios,
        );
    }

    private function moneyPositive(mixed $value): bool
    {
        return Money::fromDecimal((string) ($value ?: '0'))->minorAmount() > 0;
    }

    private function startRun(Request $request, ToolRunRecorder $recorder, ToolPersistenceAuthorizer $persistence, ToolFeatureRequestAuthorizer $features, Tool $module, CalculationInput $input): ?ToolRunHandle
    {
        if (! $request->user() || ! $features->allows($module, 'history', $request) || ! $persistence->allowsHistory($module, $request->user())) {
            return null;
        }
        $referenceMonth = $input->periodicity === 'monthly' && $input->month !== null ? $input->month : $input->quarter * 3;
        $month = str_pad((string) $referenceMonth, 2, '0', STR_PAD_LEFT);

        return $recorder->start($module, new RuleVersion('2026.1.0'), ReferenceDate::fromString('2026-'.$month.'-01'), $input->toArray(), $request->user()->id);
    }
}
