<?php

declare(strict_types=1);

namespace App\Tools\AdmissionSimulator\Presentation\Controllers;

use App\Http\Controllers\Controller;
use App\Tools\AdmissionSimulator\Application\Actions\CalculateTool;
use App\Tools\AdmissionSimulator\Application\Actions\ShowToolPage;
use App\Tools\AdmissionSimulator\Presentation\Requests\ExecuteToolRequest;
use Illuminate\Contracts\View\View;

final class ToolController extends Controller
{
    public function index(ShowToolPage $page): View
    {
        return view('tools-simulador-admissao::index', $page->execute());
    }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action): View
    {
        return view('tools-simulador-admissao::index', [
            ...app(ShowToolPage::class)->execute(),
            'result' => $action->execute($request->validated()),
        ]);
    }
}
