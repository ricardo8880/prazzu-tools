<?php

declare(strict_types=1);

namespace App\Tools\EcadRoyaltySimulator\Presentation\Controllers;

use App\Core\Access\Services\ToolFeatureRequestAuthorizer;
use App\Http\Controllers\Controller;
use App\Tools\EcadRoyaltySimulator\Application\Actions\CalculateTool;
use App\Tools\EcadRoyaltySimulator\Application\Actions\ShowToolPage;
use App\Tools\EcadRoyaltySimulator\Application\Data\CalculationInput;
use App\Tools\EcadRoyaltySimulator\Presentation\Requests\ExecuteToolRequest;
use App\Tools\EcadRoyaltySimulator\Tool;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class ToolController extends Controller
{
    public function index(Request $request, ShowToolPage $page): View
    {
        return view('tools-simulador-ecad-direitos-autorais::index', $page->execute());
    }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action, ShowToolPage $page, ToolFeatureRequestAuthorizer $features, Tool $module): View
    {
        $data = $request->validated();
        $project = (bool) ($data['project_periods'] ?? false);
        $features->requireIf($project, $module, 'period_projection', $request);
        $periods = $project ? (int) ($data['periods'] ?? 1) : 1;
        $result = $action->execute(new CalculationInput(
            method: $data['method'],
            udaValue: $data['uda_value'],
            udaQuantity: $data['uda_quantity'] ?? null,
            areaSquareMeters: $data['area_square_meters'] ?? null,
            udaPerSquareMeter: $data['uda_per_square_meter'] ?? null,
            referenceAmount: $data['reference_amount'] ?? null,
            percentageRate: $data['percentage_rate'] ?? null,
            periods: $periods,
        ));
        $request->flash();
        return view('tools-simulador-ecad-direitos-autorais::index', [...$page->execute(), 'result' => $result, 'showProjection' => $project]);
    }
}
