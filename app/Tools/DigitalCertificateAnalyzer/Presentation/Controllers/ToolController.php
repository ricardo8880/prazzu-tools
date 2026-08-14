<?php

declare(strict_types=1);

namespace App\Tools\DigitalCertificateAnalyzer\Presentation\Controllers;

use App\Core\Access\Services\ToolFeatureRequestAuthorizer;
use App\Core\Export\Contracts\PdfExporter;
use App\Core\Export\Services\ToolResultExportFactory;
use App\Http\Controllers\Controller;
use App\Tools\DigitalCertificateAnalyzer\Application\Actions\CalculateTool;
use App\Tools\DigitalCertificateAnalyzer\Application\Actions\ShowToolPage;
use App\Tools\DigitalCertificateAnalyzer\Application\Data\CalculationInput;
use App\Tools\DigitalCertificateAnalyzer\Presentation\Requests\ExecuteToolRequest;
use App\Tools\DigitalCertificateAnalyzer\Tool;
use DateTimeImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

final class ToolController extends Controller
{
    public function index(Request $request, ShowToolPage $page, ToolFeatureRequestAuthorizer $features, Tool $module): View
    {
        return view('tools-analisador-certificado-digital-a1::index', [...$page->execute(), 'plusEnabled' => $features->plusEnabled($module, $request)]);
    }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action, ToolFeatureRequestAuthorizer $features, Tool $module, ShowToolPage $page): View
    {
        $plusEnabled = $features->plusEnabled($module, $request);
        try {
            $result = $action->execute($this->input($request, $plusEnabled));
        } catch (InvalidArgumentException $exception) {
            throw ValidationException::withMessages(['certificate_file' => $exception->getMessage()]);
        }

        return view('tools-analisador-certificado-digital-a1::index', [...$page->execute(), 'result' => $result, 'plusEnabled' => $plusEnabled]);
    }

    public function export(ExecuteToolRequest $request, CalculateTool $action, ToolFeatureRequestAuthorizer $features, Tool $module, ToolResultExportFactory $documents, PdfExporter $pdf): Response
    {
        $features->require($module, 'technical_report', $request);
        try {
            $input = $this->input($request, true);
            $result = $action->execute($input);
        } catch (InvalidArgumentException $exception) {
            throw ValidationException::withMessages(['certificate_file' => $exception->getMessage()]);
        }

        $safeInput = ['arquivo' => $input->originalName, 'tamanho_bytes' => $input->size, 'analisado_em' => $input->referenceDate->format(DATE_ATOM)];
        return $pdf->download($documents->pdf('Relatório técnico de Certificado Digital A1', 'certificado-digital-a1-'.now()->format('Y-m-d'), $result, $safeInput));
    }

    private function input(ExecuteToolRequest $request, bool $technical): CalculationInput
    {
        $file = $request->file('certificate_file');
        if ($file === null || ! $file->isValid()) throw new InvalidArgumentException('O upload do certificado não foi concluído corretamente.');
        $contents = file_get_contents($file->getRealPath());
        if (! is_string($contents) || $contents === '') throw new InvalidArgumentException('O certificado enviado está vazio ou não pôde ser lido.');

        return new CalculationInput($contents, (string) $request->input('password'), $file->getClientOriginalName(), (int) $file->getSize(), new DateTimeImmutable('now'), $technical);
    }
}
