<?php

declare(strict_types=1);

namespace App\Tools\ProfitDistributionBalanceSimulator\Presentation\Controllers;

use App\Core\Access\Services\ToolFeatureRequestAuthorizer;
use App\Core\Export\Contracts\PdfExporter;
use App\Core\Export\Contracts\SpreadsheetExporter;
use App\Core\Export\Services\ToolResultExportFactory;
use App\Http\Controllers\Controller;
use App\Tools\ProfitDistributionBalanceSimulator\Application\Actions\CalculateTool;
use App\Tools\ProfitDistributionBalanceSimulator\Application\Actions\ShowToolPage;
use App\Tools\ProfitDistributionBalanceSimulator\Application\Data\CalculationInput;
use App\Tools\ProfitDistributionBalanceSimulator\Presentation\Requests\ExecuteToolRequest;
use App\Tools\ProfitDistributionBalanceSimulator\Tool;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ToolController extends Controller
{
    public function index(Request $request, ShowToolPage $page, ToolFeatureRequestAuthorizer $features, Tool $module): View
    {
        return view('tools-simulador-distribuicao-lucros-balanco::index', [...$page->execute(), 'plusEnabled' => $features->plusEnabled($module, $request)]);
    }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action, ToolFeatureRequestAuthorizer $features, Tool $module, ShowToolPage $page): View
    {
        $data = $request->validated();
        $plusEnabled = $features->plusEnabled($module, $request);
        $usesPlanning = (int) ($data['planning_months'] ?? 1) > 1
            || (bool) ($data['simulate_bookkeeping'] ?? false)
            || trim((string) ($data['monthly_pro_labore'] ?? '0')) !== '' && trim((string) ($data['monthly_pro_labore'] ?? '0')) !== '0'
            || trim((string) ($data['monthly_growth_rate'] ?? '0')) !== '' && trim((string) ($data['monthly_growth_rate'] ?? '0')) !== '0'
            || trim((string) ($data['operating_expenses'] ?? '0')) !== '' && trim((string) ($data['operating_expenses'] ?? '0')) !== '0'
            || trim((string) ($data['other_expenses'] ?? '0')) !== '' && trim((string) ($data['other_expenses'] ?? '0')) !== '0';
        $features->requireIf($usesPlanning, $module, 'planning', $request);
        if (! $plusEnabled) {
            $data = [...$data, 'prior_distributions' => '0', 'monthly_pro_labore' => '0', 'monthly_growth_rate' => '0', 'planning_months' => 1, 'simulate_bookkeeping' => false, 'operating_expenses' => '0', 'other_expenses' => '0'];
        }
        $input = $this->input($data);
        $result = $action->execute($input);
        $request->flash();

        return view('tools-simulador-distribuicao-lucros-balanco::index', [...$page->execute(), 'result' => $result, 'calculationInput' => $data, 'plusEnabled' => $plusEnabled]);
    }

    public function exportCurrent(ExecuteToolRequest $request, CalculateTool $action, ToolResultExportFactory $documents, PdfExporter $pdf, SpreadsheetExporter $spreadsheet, string $format): Response
    {
        abort_unless(in_array($format, ['pdf', 'xlsx'], true), 404);
        $input = $this->input($request->validated());
        $result = $action->execute($input);
        $name = 'distribuicao-lucros-balanco-'.now()->format('Y-m-d');

        return $format === 'pdf' ? $pdf->download($documents->pdf('Distribuição de Lucros — Balanço × sem Balanço', $name, $result, $input->toArray())) : $spreadsheet->download($documents->spreadsheet($name, $result, $input->toArray()));
    }

    private function input(array $data): CalculationInput
    {
        return new CalculationInput(
            annualRevenue: (string) $data['annual_revenue'], accountingProfit: (string) $data['accounting_profit'], referenceMargin: (string) $data['reference_margin'], taxesOnRevenue: (string) $data['taxes_on_revenue'], priorDistributions: (string) ($data['prior_distributions'] ?? '0'), monthlyProLabore: (string) ($data['monthly_pro_labore'] ?? '0'), monthlyGrowthRate: (string) ($data['monthly_growth_rate'] ?? '0'), planningMonths: (int) ($data['planning_months'] ?? 1), simulateBookkeeping: (bool) ($data['simulate_bookkeeping'] ?? false), operatingExpenses: (string) ($data['operating_expenses'] ?? '0'), otherExpenses: (string) ($data['other_expenses'] ?? '0'),
        );
    }
}
