<?php

declare(strict_types=1);

namespace App\Tools\TaxReformSimulator\Presentation\Controllers;

use App\Core\Access\Services\ToolFeatureRequestAuthorizer;
use App\Http\Controllers\Controller;
use App\Tools\TaxReformSimulator\Application\Actions\CalculateTool;
use App\Tools\TaxReformSimulator\Application\Actions\ShowToolPage;
use App\Tools\TaxReformSimulator\Application\Data\CalculationInput;
use App\Tools\TaxReformSimulator\Presentation\Requests\ExecuteToolRequest;
use App\Tools\TaxReformSimulator\Tool;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class ToolController extends Controller
{
    public function index(Request $request, ShowToolPage $page): View { return view('tools-sales-tax-calculator::index', $page->execute()); }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action, ShowToolPage $page, ToolFeatureRequestAuthorizer $features, Tool $module): View
    {
        $data = $request->validated();
        $showDiagnostics = (bool) ($data['show_diagnostics'] ?? false);
        $features->requireIf($showDiagnostics, $module, 'combined_rate_diagnostics', $request);
        $result = $action->execute(new CalculationInput((string) $data['price'], (string) $data['state_rate'], (string) ($data['local_rate'] ?? '0'), (string) ($data['district_rate'] ?? '0'), (int) $data['quantity'], (bool) ($data['tax_included'] ?? false)));
        $request->flash();

        return view('tools-sales-tax-calculator::index', [...$page->execute(), 'result' => $result, 'showDiagnostics' => $showDiagnostics]);
    }
}
