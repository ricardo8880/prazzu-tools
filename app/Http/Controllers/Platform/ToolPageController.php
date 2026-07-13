<?php

namespace App\Http\Controllers\Platform;

use App\Core\Tools\ToolCatalog;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

final class ToolPageController extends Controller
{
    public function __construct(private readonly ToolCatalog $catalog)
    {
    }

    public function show(string $tool): View
    {
        $definition = $this->catalog->find($tool);

        abort_if($definition === null, 404, 'Ferramenta não encontrada.');

        return view('pages.tools.show', [
            'tool' => $definition,
            'relatedTools' => $this->catalog->related($tool),
        ]);
    }
}
