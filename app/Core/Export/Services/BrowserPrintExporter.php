<?php

declare(strict_types=1);

namespace App\Core\Export\Services;

use App\Core\Export\Data\PrintableDocument;
use Illuminate\Contracts\View\View;

final class BrowserPrintExporter
{
    public function render(PrintableDocument $document): View
    {
        return view('exports.browser-print', [
            'document' => $document,
        ]);
    }
}
