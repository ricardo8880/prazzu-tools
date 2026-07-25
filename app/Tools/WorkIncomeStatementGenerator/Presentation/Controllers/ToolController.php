<?php

declare(strict_types=1);

namespace App\Tools\WorkIncomeStatementGenerator\Presentation\Controllers;

use App\Http\Controllers\Controller;
use App\Tools\WorkIncomeStatementGenerator\Application\Actions\CalculateTool;
use App\Tools\WorkIncomeStatementGenerator\Application\Actions\ShowToolPage;
use App\Tools\WorkIncomeStatementGenerator\Presentation\Requests\ExecuteToolRequest;
use Illuminate\Contracts\View\View;

final class ToolController extends Controller
{
    public function index(ShowToolPage $page): View
    {
        return view('tools-declaracao-trabalho-renda::index', $page->execute());
    }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action): View
    {
        return view('tools-declaracao-trabalho-renda::index', [
            ...app(ShowToolPage::class)->execute(),
            'result' => $action->execute($request->validated()),
        ]);
    }
}
