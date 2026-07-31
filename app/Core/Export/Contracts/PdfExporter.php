<?php

declare(strict_types=1);

namespace App\Core\Export\Contracts;

use App\Core\Export\Data\PdfDocument;
use Symfony\Component\HttpFoundation\Response;

interface PdfExporter
{
    public function download(PdfDocument $document): Response;
}
