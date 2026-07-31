<?php

declare(strict_types=1);

namespace App\Core\Export\Contracts;

use App\Core\Export\Data\SpreadsheetDocument;
use Symfony\Component\HttpFoundation\Response;

interface SpreadsheetExporter
{
    public function download(SpreadsheetDocument $document): Response;
}
