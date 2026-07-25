<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreSimulator\Presentation\Controllers;

use App\Http\Controllers\Controller;
use App\Tools\ProLaboreSimulator\Application\Data\CalculationInput;
use App\Tools\ProLaboreSimulator\Domain\Services\Calculator;
use App\Tools\ProLaboreSimulator\Presentation\Requests\ExecuteToolRequest;
use Illuminate\Contracts\View\View;

final class ToolController extends Controller
{
    public function index(): View
    {
        return view('tools-simulador-pro-labore-ideal::index');
    }

    public function calculate(ExecuteToolRequest $request, Calculator $calculator): View
    {
        $data = $request->validated();
        $result = $calculator->calculate(new CalculationInput(
            competence: $data['competence'],
            companyRegime: $data['company_regime'],
            grossProLabore: $data['gross_pro_labore'],
            dependents: (int) ($data['dependents'] ?? 0),
            otherOfficialSocialSecurity: $data['other_official_social_security'] ?? '0',
        ));

        return view('tools-simulador-pro-labore-ideal::index', compact('result'));
    }
}
