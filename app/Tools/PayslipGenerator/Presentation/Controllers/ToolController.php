<?php

declare(strict_types=1);

namespace App\Tools\PayslipGenerator\Presentation\Controllers;

use App\Http\Controllers\Controller;
use App\Tools\PayslipGenerator\Application\Actions\CalculateTool;
use App\Tools\PayslipGenerator\Application\Actions\ShowToolPage;
use App\Tools\PayslipGenerator\Presentation\Requests\ExecuteToolRequest;
use Illuminate\Contracts\View\View;

final class ToolController extends Controller
{
    public function index(ShowToolPage $page): View
    {
        return view('tools-gerador-holerite::index', $page->execute());
    }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action): View
    {
        return view('tools-gerador-holerite::index', [
            ...app(ShowToolPage::class)->execute(),
            'result' => $action->execute($request->validated()),
        ]);
    }
}
