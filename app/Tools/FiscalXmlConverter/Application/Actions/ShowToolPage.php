<?php

declare(strict_types=1);

namespace App\Tools\FiscalXmlConverter\Application\Actions;

use App\Core\Tools\Data\ToolManifest;
use App\Tools\FiscalXmlConverter\Tool;
use Illuminate\Http\Request;

final readonly class ShowToolPage
{
    public function __construct(private Tool $tool, private Request $request) {}

    /** @return array{tool: ToolManifest, result: array|null} */
    public function execute(): array
    {
        return [
            'tool' => $this->tool->manifest(),
            'result' => $this->request->session()->get('fiscal_xml_result'),
        ];
    }
}
