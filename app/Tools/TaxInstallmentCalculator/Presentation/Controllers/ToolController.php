<?php

declare(strict_types=1);

namespace App\Tools\TaxInstallmentCalculator\Presentation\Controllers;

use App\Core\Access\Services\ToolFeatureRequestAuthorizer;
use App\Core\Export\Contracts\PdfExporter;
use App\Core\Export\Contracts\SpreadsheetExporter;
use App\Core\Export\Services\ToolResultExportFactory;
use App\Core\Money\Money;
use App\Http\Controllers\Controller;
use App\Tools\TaxInstallmentCalculator\Application\Actions\CalculateTool;
use App\Tools\TaxInstallmentCalculator\Application\Actions\ShowToolPage;
use App\Tools\TaxInstallmentCalculator\Application\Data\CalculationInput;
use App\Tools\TaxInstallmentCalculator\Presentation\Requests\ExecuteToolRequest;
use App\Tools\TaxInstallmentCalculator\Tool;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ToolController extends Controller
{
    public function index(Request $request, ShowToolPage $page, ToolFeatureRequestAuthorizer $features, Tool $module): View
    {
        return view('tools-calculadora-parcelamento-tributario::index', [...$page->execute(), 'plusEnabled' => $features->plusEnabled($module, $request)]);
    }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action, ToolFeatureRequestAuthorizer $features, Tool $module, ShowToolPage $page): View
    {
        $data = $request->validated();
        $hasScenario = count(array_filter($data['scenarios'] ?? [], fn ($s) => trim((string) ($s['name'] ?? $s['entry_amount'] ?? $s['installments'] ?? $s['monthly_charge'] ?? '')) !== '')) > 0;
        $features->requireIf($this->moneyPositive($data['entry_amount'] ?? '0') || $hasScenario, $module, 'scenario_comparison', $request);
        $input = $this->input($data); $result = $action->execute($input); $request->flash();
        return view('tools-calculadora-parcelamento-tributario::index', [...$page->execute(), 'result' => $result, 'calculationInput' => $data, 'plusEnabled' => $features->plusEnabled($module, $request)]);
    }

    public function exportCurrent(ExecuteToolRequest $request, CalculateTool $action, ToolResultExportFactory $documents, PdfExporter $pdf, SpreadsheetExporter $spreadsheet, string $format): Response
    {
        abort_unless(in_array($format, ['pdf', 'xlsx'], true), 404); $input = $this->input($request->validated()); $result = $action->execute($input); $filename = 'parcelamento-tributario-'.now()->format('Y-m-d');
        return $format === 'pdf' ? $pdf->download($documents->pdf('Calculadora de Parcelamento Tributário', $filename, $result, $input->toArray())) : $spreadsheet->download($documents->spreadsheet($filename, $result, $input->toArray()));
    }

    private function input(array $data): CalculationInput
    {
        $debt = (string) $data['debt_amount']; $scenarios = [['name' => 'Cenário principal', 'debt' => $debt, 'entry' => (string) ($data['entry_amount'] ?? '0'), 'installments' => (int) $data['installments'], 'monthly_charge' => (string) $data['monthly_charge']]];
        foreach (($data['scenarios'] ?? []) as $index => $scenario) {
            $name = trim((string) ($scenario['name'] ?? '')); $entry = trim((string) ($scenario['entry_amount'] ?? '')); $installments = trim((string) ($scenario['installments'] ?? '')); $charge = trim((string) ($scenario['monthly_charge'] ?? ''));
            if ($name === '' && $entry === '' && $installments === '' && $charge === '') continue;
            $scenarios[] = ['name' => $name !== '' ? $name : 'Cenário '.($index + 2), 'debt' => $debt, 'entry' => $entry !== '' ? $entry : '0', 'installments' => $installments !== '' ? (int) $installments : (int) $data['installments'], 'monthly_charge' => $charge !== '' ? $charge : (string) $data['monthly_charge']];
        }
        return new CalculationInput($scenarios);
    }

    private function moneyPositive(mixed $value): bool { return Money::fromDecimal((string) ($value ?: '0'))->minorAmount() > 0; }
}
