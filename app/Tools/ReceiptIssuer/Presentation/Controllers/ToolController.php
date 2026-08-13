<?php

declare(strict_types=1);

namespace App\Tools\ReceiptIssuer\Presentation\Controllers;

use App\Core\Access\Services\ToolFeatureRequestAuthorizer;
use App\Core\Access\Services\ToolPersistenceAuthorizer;
use App\Core\Dates\ReferenceDate;
use App\Core\Exceptions\InvalidValue;
use App\Core\Export\Contracts\PdfExporter;
use App\Core\Export\Contracts\SpreadsheetExporter;
use App\Core\Export\Data\PdfDocument;
use App\Core\Export\Services\StructuredResultExportFactory;
use App\Core\Tools\History\Contracts\ToolRunRecorder;
use App\Core\Tools\History\Data\RuleVersion;
use App\Http\Controllers\Controller;
use App\Tools\ReceiptIssuer\Application\Actions\BuildCalculationInput;
use App\Tools\ReceiptIssuer\Application\Actions\CalculateTool;
use App\Tools\ReceiptIssuer\Application\Actions\GenerateReceiptBatch;
use App\Tools\ReceiptIssuer\Application\Actions\ManageReceiptHistory;
use App\Tools\ReceiptIssuer\Application\Actions\ManageReceiptPartyProfiles;
use App\Tools\ReceiptIssuer\Application\Actions\ShowToolPage;
use App\Tools\ReceiptIssuer\Presentation\Requests\BatchIssueRequest;
use App\Tools\ReceiptIssuer\Presentation\Requests\ExecuteToolRequest;
use App\Tools\ReceiptIssuer\Presentation\Requests\StorePartyProfileRequest;
use App\Tools\ReceiptIssuer\Tool;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

final class ToolController extends Controller
{
    public function index(Request $request, ShowToolPage $page, ManageReceiptHistory $history, ManageReceiptPartyProfiles $profiles): View
    {
        return view('tools-emissor-de-recibos::index', [
            ...$page->execute(),
            'result' => $request->session()->get('receipt_result'),
            'recentHistory' => $request->user() ? $history->recent((int) $request->user()->getAuthIdentifier()) : [],
            'partyProfiles' => $request->user() ? $profiles->all((int) $request->user()->getAuthIdentifier()) : collect(),
        ]);
    }

    public function exportCurrent(
        ExecuteToolRequest $request,
        BuildCalculationInput $build,
        CalculateTool $calculate,
        StructuredResultExportFactory $documents,
        PdfExporter $pdf,
        SpreadsheetExporter $spreadsheet,
        ToolFeatureRequestAuthorizer $features,
        Tool $module,
        string $format,
    ): Response {
        abort_unless(in_array($format, ['pdf', 'xlsx'], true), 404);
        try {
            $input = $request->validated();
            $features->requireIf($this->hasBranding($input), $module, 'custom_branding', $request);
            $result = $this->withBranding($calculate->execute($build->execute($input))->toArray(), $input);
        } catch (InvalidValue|InvalidArgumentException $exception) {
            throw ValidationException::withMessages(['receipt' => $exception->getMessage()]);
        }
        if ($format === 'pdf') {
            return $this->downloadPdf($result, $pdf);
        }

        return $spreadsheet->download($documents->spreadsheet('recibo-'.now()->format('Y-m-d'), $result, $input));
    }

    public function printCurrent(
        ExecuteToolRequest $request,
        BuildCalculationInput $build,
        CalculateTool $calculate,
        ToolFeatureRequestAuthorizer $features,
        Tool $module,
    ): View {
        try {
            $input = $request->validated();
            $features->requireIf($this->hasBranding($input), $module, 'custom_branding', $request);
            $result = $this->withBranding($calculate->execute($build->execute($input))->toArray(), $input);
        } catch (InvalidValue|InvalidArgumentException $exception) {
            throw ValidationException::withMessages(['receipt' => $exception->getMessage()]);
        }

        $receipt = $result['details']['receipt'] ?? null;
        if (! is_array($receipt)) {
            throw ValidationException::withMessages(['receipt' => 'Não foi possível preparar o recibo para impressão.']);
        }

        return view('exports.printable-document', [
            'title' => 'Recibo nº '.$receipt['number'],
            'contentView' => 'tools-emissor-de-recibos::pdf.receipt',
            'contentData' => ['receipt' => $receipt],
            'generatedAt' => now()->format('d/m/Y H:i'),
            'summaryLabel' => 'Valor',
            'summaryValue' => $receipt['amount'],
        ]);
    }

    public function issue(
        ExecuteToolRequest $request,
        BuildCalculationInput $build,
        CalculateTool $calculate,
        ToolRunRecorder $recorder,
        ToolPersistenceAuthorizer $persistence,
        Tool $module,
        ToolFeatureRequestAuthorizer $features,
        ShowToolPage $page,
        ManageReceiptHistory $history,
        ManageReceiptPartyProfiles $profiles,
    ): View {
        $input = $request->validated();
        $features->requireIf($this->hasBranding($input), $module, 'custom_branding', $request);

        try {
            $result = $this->withBranding($calculate->execute($build->execute($input))->toArray(), $input);
        } catch (InvalidValue|InvalidArgumentException $exception) {
            throw ValidationException::withMessages(['receipt' => $exception->getMessage()]);
        }

        $saved = false;
        if ($persistence->allowsHistory($module, $request->user())) {
            $run = $recorder->start(
                $module,
                new RuleVersion('2026.1.0'),
                ReferenceDate::fromString((string) $input['issued_at']),
                $input,
                (int) $request->user()->getAuthIdentifier(),
            );
            $recorder->succeed($run, $result);
            $saved = true;
        }

        $request->flash();

        return view('tools-emissor-de-recibos::index', [
            ...$page->execute(),
            'result' => $result,
            'historySaved' => $saved,
            'recentHistory' => $request->user() ? $history->recent((int) $request->user()->getAuthIdentifier()) : [],
            'partyProfiles' => $request->user() ? $profiles->all((int) $request->user()->getAuthIdentifier()) : collect(),
        ]);
    }

    public function history(Request $request, ManageReceiptHistory $history): View
    {
        return view('tools-emissor-de-recibos::history.index', [
            'runs' => $history->paginate(
                (int) $request->user()->getAuthIdentifier(),
                max(1, $request->integer('page', 1)),
            ),
        ]);
    }

    public function repeatHistory(Request $request, string $run, ManageReceiptHistory $history): RedirectResponse
    {
        $entry = $history->owned($run, (int) $request->user()->getAuthIdentifier());

        return redirect()->route('tools.emissor-de-recibos.index')
            ->withInput($entry->input)
            ->with('receipt_result', $entry->result)
            ->with('history_message', 'Recibo recuperado. Revise os dados antes de emitir novamente.');
    }

    public function destroyHistory(Request $request, string $run, ManageReceiptHistory $history): RedirectResponse
    {
        $history->delete($run, (int) $request->user()->getAuthIdentifier());

        return back()->with('history_message', 'Recibo removido do histórico.');
    }

    public function exportHistory(Request $request, string $run, string $format, ManageReceiptHistory $history, StructuredResultExportFactory $documents, PdfExporter $pdf, SpreadsheetExporter $spreadsheet): Response
    {
        abort_unless(in_array($format, ['pdf', 'xlsx'], true), 404);
        $entry = $history->owned($run, (int) $request->user()->getAuthIdentifier());

        return $format === 'pdf' ? $this->downloadPdf($entry->result, $pdf) : $spreadsheet->download($documents->spreadsheet('recibo-historico', $entry->result, $entry->input));
    }

    public function batch(): View
    {
        return view('tools-emissor-de-recibos::batch.index');
    }

    public function issueBatch(BatchIssueRequest $request, GenerateReceiptBatch $batch): View
    {
        $result = $batch->execute($request->file('file'));

        return view('exports.printable-document', [
            'title' => 'Recibos em lote',
            'contentView' => 'tools-emissor-de-recibos::pdf.batch',
            'contentData' => $result,
            'generatedAt' => now()->format('d/m/Y H:i'),
        ]);
    }

    public function profiles(Request $request, ManageReceiptPartyProfiles $profiles): View
    {
        return view('tools-emissor-de-recibos::profiles.index', [
            'profiles' => $profiles->all((int) $request->user()->getAuthIdentifier()),
        ]);
    }

    public function storeProfile(StorePartyProfileRequest $request, ManageReceiptPartyProfiles $profiles): RedirectResponse
    {
        $profiles->save((int) $request->user()->getAuthIdentifier(), $request->validated());

        return back()->with('profile_message', 'Perfil salvo e disponível para reutilização.');
    }

    public function useProfile(Request $request, int $profile, ManageReceiptPartyProfiles $profiles): RedirectResponse
    {
        $saved = $profiles->owned($profile, (int) $request->user()->getAuthIdentifier());
        $prefix = $saved->party_type === 'payer' ? 'payer' : 'payee';

        return redirect()->route('tools.emissor-de-recibos.index')->withInput([
            $prefix.'_name' => $saved->name,
            $prefix.'_document_type' => $saved->document_type,
            $prefix.'_document' => $saved->document,
        ])->with('profile_message', 'Perfil aplicado. Complete os demais dados do recibo.');
    }

    public function destroyProfile(Request $request, int $profile, ManageReceiptPartyProfiles $profiles): RedirectResponse
    {
        $profiles->delete($profile, (int) $request->user()->getAuthIdentifier());

        return back()->with('profile_message', 'Perfil removido.');
    }

    /** @param array<string,mixed> $input */
    private function hasBranding(array $input): bool
    {
        return filled($input['brand_name'] ?? null) || filled($input['brand_document'] ?? null) || filled($input['brand_footer'] ?? null);
    }

    /** @param array<string,mixed> $result @param array<string,mixed> $input @return array<string,mixed> */
    private function withBranding(array $result, array $input): array
    {
        if (! $this->hasBranding($input)) {
            return $result;
        }
        $result['details']['receipt']['branding'] = [
            'name' => trim((string) ($input['brand_name'] ?? '')),
            'document' => trim((string) ($input['brand_document'] ?? '')),
            'footer' => trim((string) ($input['brand_footer'] ?? '')),
        ];

        return $result;
    }

    private function downloadPdf(array $result, PdfExporter $pdf): Response
    {
        $receipt = $result['details']['receipt'] ?? null;
        if (! is_array($receipt)) {
            throw ValidationException::withMessages(['receipt' => 'Não foi possível preparar o recibo para exportação.']);
        }

        return $pdf->download(new PdfDocument(filename: 'recibo-'.$receipt['number'], view: 'tools-emissor-de-recibos::pdf.receipt', data: ['receipt' => $receipt]));
    }
}
