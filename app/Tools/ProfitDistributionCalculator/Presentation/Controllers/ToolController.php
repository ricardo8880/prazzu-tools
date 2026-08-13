<?php

declare(strict_types=1);

namespace App\Tools\ProfitDistributionCalculator\Presentation\Controllers;

use App\Core\Access\Services\ToolFeatureRequestAuthorizer;
use App\Core\Export\Contracts\PdfExporter;
use App\Core\Export\Contracts\SpreadsheetExporter;
use App\Core\Export\Services\ToolResultExportFactory;
use App\Http\Controllers\Controller;
use App\Tools\ProfitDistributionCalculator\Application\Data\CalculationInput;
use App\Tools\ProfitDistributionCalculator\Domain\Services\Calculator;
use App\Tools\ProfitDistributionCalculator\Presentation\Requests\ExecuteToolRequest;
use App\Tools\ProfitDistributionCalculator\Tool;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ToolController extends Controller
{
    public function index(Request $request, ToolFeatureRequestAuthorizer $features, Tool $module): View
    {
        return view('tools-distribuicao-de-lucros::index', ['plusEnabled' => $features->plusEnabled($module, $request)]);
    }

    public function calculate(ExecuteToolRequest $request, Calculator $calculator, ToolFeatureRequestAuthorizer $features, Tool $module): View
    {
        $data = $request->validated();
        $features->requireIf($this->hasMultiplePartners($data), $module, 'partners', $request);
        $result = $calculator->calculate($this->input($data));

        return view('tools-distribuicao-de-lucros::index', ['result' => $result, 'plusEnabled' => $features->plusEnabled($module, $request)]);
    }

    public function downloadPdf(ExecuteToolRequest $request, Calculator $calculator, PdfExporter $exporter, ToolResultExportFactory $documents, ToolFeatureRequestAuthorizer $features, Tool $module): Response
    {
        $data = $request->validated();
        $features->requireIf($this->hasMultiplePartners($data), $module, 'partners', $request);
        $result = $calculator->calculate($this->input($data));

        return $exporter->download($documents->pdf('Relatório de Distribuição de Lucros', 'distribuicao-de-lucros-'.now()->format('Y-m-d'), $result, $data));
    }

    public function downloadExcel(ExecuteToolRequest $request, Calculator $calculator, SpreadsheetExporter $exporter, ToolResultExportFactory $documents, ToolFeatureRequestAuthorizer $features, Tool $module): Response
    {
        $data = $request->validated();
        $features->requireIf($this->hasMultiplePartners($data), $module, 'partners', $request);
        $result = $calculator->calculate($this->input($data));

        return $exporter->download($documents->spreadsheet('distribuicao-de-lucros-'.now()->format('Y-m-d'), $result, $data));
    }

    private function hasMultiplePartners(array $data): bool
    {
        return count(array_filter(
            $data['partners'] ?? [],
            static fn (array $partner): bool => trim((string) ($partner['ownership_percentage'] ?? '')) !== '',
        )) > 0;
    }

    private function input(array $data): CalculationInput
    {
        return new CalculationInput(
            partnerLabel: $data['partner_label'] ?? 'Sócio', ownershipPercentage: $data['ownership_percentage'], accountingProfit: $data['accounting_profit'],
            accumulatedLosses: $data['accumulated_losses'] ?? '0', reservesAndUnavailableAmounts: $data['reserves_and_unavailable_amounts'] ?? '0',
            adjustments: $data['adjustments'] ?? '0', priorDistributions: $data['prior_distributions'] ?? '0', intendedDistribution: $data['intended_distribution'], partners: $data['partners'] ?? [],
        );
    }
}
