<?php

declare(strict_types=1);

namespace App\Tools\IncomeStatementGenerator\Presentation\Controllers;

use App\Http\Controllers\Controller;
use App\Tools\IncomeStatementGenerator\Application\Actions\CalculateTool;
use App\Tools\IncomeStatementGenerator\Application\Actions\ShowToolPage;
use App\Tools\IncomeStatementGenerator\Presentation\Requests\ExecuteToolRequest;
use Illuminate\Contracts\View\View;

final class ToolController extends Controller
{
    public function index(ShowToolPage $page): View
    {
        return view('tools-declaracao-rendimentos::index', $page->execute());
    }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action): View
    {
        return view('tools-declaracao-rendimentos::index', [
            ...app(ShowToolPage::class)->execute(),
            'result' => $action->execute($request->validated()),
        ]);
    }
}
