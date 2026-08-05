<?php

declare(strict_types=1);

namespace App\Tools\ProfitDistributionCalculator\Presentation\Controllers;

use App\Core\Export\Contracts\PdfExporter;
use App\Core\Export\Contracts\SpreadsheetExporter;
use App\Core\Export\Services\ToolResultExportFactory;
use App\Http\Controllers\Controller;
use App\Tools\ProfitDistributionCalculator\Application\Data\CalculationInput;
use App\Tools\ProfitDistributionCalculator\Domain\Services\Calculator;
use App\Tools\ProfitDistributionCalculator\Presentation\Requests\ExecuteToolRequest;
use Illuminate\Contracts\View\View;

final class ToolController extends Controller
{
    public function index(): View
    {
        return view('tools-distribuicao-de-lucros::index');
    }

    public function calculate(ExecuteToolRequest $request, Calculator $calculator): View
    {
        $data = $request->validated();
        $result = $calculator->calculate($this->input($data));

        return view('tools-distribuicao-de-lucros::index', compact('result'));
    }

    public function downloadPdf(ExecuteToolRequest $request, Calculator $calculator, PdfExporter $exporter, ToolResultExportFactory $documents): \Symfony\Component\HttpFoundation\Response
    {
        $data = $request->validated();
        $result = $calculator->calculate($this->input($data));
        return $exporter->download($documents->pdf('Relatório de Distribuição de Lucros', 'distribuicao-de-lucros-'.now()->format('Y-m-d'), $result, $data));
    }

    public function downloadExcel(ExecuteToolRequest $request, Calculator $calculator, SpreadsheetExporter $exporter, ToolResultExportFactory $documents): \Symfony\Component\HttpFoundation\Response
    {
        $data = $request->validated();
        $result = $calculator->calculate($this->input($data));
        return $exporter->download($documents->spreadsheet('distribuicao-de-lucros-'.now()->format('Y-m-d'), $result, $data));
    }

    private function input(array $data): CalculationInput
    {
        return new CalculationInput(
            partnerLabel: $data['partner_label'] ?? 'Sócio', ownershipPercentage: $data['ownership_percentage'], accountingProfit: $data['accounting_profit'],
            accumulatedLosses: $data['accumulated_losses'] ?? '0', reservesAndUnavailableAmounts: $data['reserves_and_unavailable_amounts'] ?? '0',
            adjustments: $data['adjustments'] ?? '0', priorDistributions: $data['prior_distributions'] ?? '0', intendedDistribution: $data['intended_distribution'],
        );
    }
}
