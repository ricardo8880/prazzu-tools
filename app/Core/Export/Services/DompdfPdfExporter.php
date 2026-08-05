<?php

declare(strict_types=1);

namespace App\Core\Export\Services;

use App\Core\Export\Contracts\PdfExporter;
use App\Core\Export\Data\PdfDocument;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Contracts\View\Factory as ViewFactory;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

final readonly class DompdfPdfExporter implements PdfExporter
{
    public function __construct(private ViewFactory $views) {}

    public function download(PdfDocument $document): Response
    {
        $html = $this->views->make($document->view, $document->data)->render();

        if (! class_exists(Dompdf::class)) {
            throw new RuntimeException('A biblioteca dompdf/dompdf não está instalada. Execute Composer antes de exportar PDFs.');
        }

        $options = new Options;
        $options->set('isRemoteEnabled', false);
        $options->set('isPhpEnabled', false);
        $options->set('isJavascriptEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper($document->paper, $document->orientation);
        $dompdf->render();

        $content = $dompdf->output();

        return new Response($content, Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => $this->contentDisposition($document->downloadFilename()),
            'Content-Length' => (string) strlen($content),
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function contentDisposition(string $filename): string
    {
        $ascii = preg_replace('/[^A-Za-z0-9._-]/', '-', $filename) ?: 'resultado.pdf';

        return sprintf("attachment; filename=\"%s\"; filename*=UTF-8''%s", $ascii, rawurlencode($filename));
    }
}
