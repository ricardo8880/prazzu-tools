<?php

declare(strict_types=1);

namespace App\Tools\MeiToMicroenterpriseSimulator\Presentation\Controllers;

use App\Core\Access\Services\ToolFeatureRequestAuthorizer;
use App\Core\Export\Contracts\PdfExporter;
use App\Core\Export\Contracts\SpreadsheetExporter;
use App\Core\Export\Services\ToolResultExportFactory;
use App\Http\Controllers\Controller;
use App\Tools\MeiToMicroenterpriseSimulator\Application\Actions\CalculateTool;
use App\Tools\MeiToMicroenterpriseSimulator\Application\Actions\ShowToolPage;
use App\Tools\MeiToMicroenterpriseSimulator\Application\Data\CalculationInput;
use App\Tools\MeiToMicroenterpriseSimulator\Presentation\Requests\ExecuteToolRequest;
use App\Tools\MeiToMicroenterpriseSimulator\Tool;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ToolController extends Controller
{
    public function index(Request $request, ShowToolPage $page, ToolFeatureRequestAuthorizer $features, Tool $module): View
    {
        return view('tools-simulador-mei-microempresa::index', [...$page->execute(), 'plusEnabled' => $features->plusEnabled($module, $request)]);
    }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action, ToolFeatureRequestAuthorizer $features, Tool $module, ShowToolPage $page): View
    {
        $data = $request->validated();
        $plusEnabled = $features->plusEnabled($module, $request);
        $features->requireIf((int) ($data['projection_years'] ?? 1) > 1 || trim((string) ($data['annual_growth_rate'] ?? '')) !== '', $module, 'annual_projection', $request);
        $features->requireIf(
            trim((string) ($data['me_effective_tax_rate'] ?? '')) !== ''
            || trim((string) ($data['monthly_accounting_cost'] ?? '')) !== ''
            || trim((string) ($data['monthly_other_cost'] ?? '')) !== ''
            || trim((string) ($data['monthly_mei_cost'] ?? '')) !== '',
            $module,
            'business_costs',
            $request,
        );
        $features->requireIf(trim((string) ($data['target_fixed_cost_burden'] ?? '')) !== '', $module, 'migration_point', $request);
        if (! $plusEnabled) {
            $data = [...$data, 'me_effective_tax_rate' => '0', 'monthly_accounting_cost' => '0', 'monthly_other_cost' => '0', 'monthly_mei_cost' => '0', 'annual_growth_rate' => '0', 'projection_years' => 1, 'target_fixed_cost_burden' => '100'];
        }
        $input = $this->input($data);
        $result = $action->execute($input);
        $request->flash();

        return view('tools-simulador-mei-microempresa::index', [...$page->execute(), 'result' => $result, 'calculationInput' => $data, 'plusEnabled' => $plusEnabled]);
    }

    public function exportCurrent(ExecuteToolRequest $request, CalculateTool $action, ToolResultExportFactory $documents, PdfExporter $pdf, SpreadsheetExporter $spreadsheet, string $format): Response
    {
        abort_unless(in_array($format, ['pdf', 'xlsx'], true), 404);
        $input = $this->input($request->validated());
        $result = $action->execute($input);
        $filename = 'simulacao-mei-microempresa-'.now()->format('Y-m-d');

        return $format === 'pdf' ? $pdf->download($documents->pdf('Simulador MEI → Microempresa', $filename, $result, $input->toArray())) : $spreadsheet->download($documents->spreadsheet($filename, $result, $input->toArray()));
    }

    private function input(array $data): CalculationInput
    {
        return new CalculationInput(currentAnnualRevenue: (string) $data['current_annual_revenue'], projectedAnnualRevenue: (string) $data['projected_annual_revenue'], meEffectiveTaxRate: (string) ($data['me_effective_tax_rate'] ?? '0'), monthlyAccountingCost: (string) ($data['monthly_accounting_cost'] ?? '0'), monthlyOtherCost: (string) ($data['monthly_other_cost'] ?? '0'), monthlyMeiCost: (string) ($data['monthly_mei_cost'] ?? '0'), annualGrowthRate: (string) ($data['annual_growth_rate'] ?? '0'), projectionYears: (int) ($data['projection_years'] ?? 1), targetFixedCostBurden: (string) ($data['target_fixed_cost_burden'] ?? '100'));
    }
}
