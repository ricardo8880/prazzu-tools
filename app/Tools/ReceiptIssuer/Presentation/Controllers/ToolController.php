<?php

declare(strict_types=1);

namespace App\Tools\ReceiptIssuer\Presentation\Controllers;

use App\Core\Access\Services\ToolPersistenceAuthorizer;
use App\Core\Dates\ReferenceDate;
use App\Core\Exceptions\InvalidValue;
use App\Core\Export\Data\PrintableDocument;
use App\Core\Export\Services\BrowserPrintExporter;
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

    public function exportPdf(
        ExecuteToolRequest $request,
        BuildCalculationInput $build,
        CalculateTool $calculate,
        BrowserPrintExporter $exporter,
    ): View {
        try {
            $result = $calculate->execute($build->execute($request->validated()))->toArray();
        } catch (InvalidValue|InvalidArgumentException $exception) {
            throw ValidationException::withMessages(['receipt' => $exception->getMessage()]);
        }

        return $this->renderPdf($result, $exporter);
    }

    public function issue(
        ExecuteToolRequest $request,
        BuildCalculationInput $build,
        CalculateTool $calculate,
        ToolRunRecorder $recorder,
        ToolPersistenceAuthorizer $persistence,
        Tool $module,
        ShowToolPage $page,
        ManageReceiptHistory $history,
        ManageReceiptPartyProfiles $profiles,
    ): View {
        $input = $request->validated();

        try {
            $result = $calculate->execute($build->execute($input))->toArray();
        } catch (InvalidValue|InvalidArgumentException $exception) {
            throw ValidationException::withMessages(['receipt' => $exception->getMessage()]);
        }

        $saved = false;
        if ($persistence->allowsHistory($module, $request->user())) {
            $run = $recorder->start(
                $module,
                new RuleVersion('2026.1'),
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

    public function exportHistory(Request $request, string $run, ManageReceiptHistory $history, BrowserPrintExporter $exporter): View
    {
        $entry = $history->owned($run, (int) $request->user()->getAuthIdentifier());

        return $this->renderPdf($entry->result, $exporter);
    }


    public function batch(): View
    {
        return view('tools-emissor-de-recibos::batch.index');
    }

    public function issueBatch(BatchIssueRequest $request, GenerateReceiptBatch $batch, BrowserPrintExporter $exporter): View
    {
        $result = $batch->execute($request->file('file'));

        return $exporter->render(new PrintableDocument(
            title: 'Recibos em lote',
            subtitle: $result['total'].' linha(s) processada(s), '.count($result['receipts']).' recibo(s) válido(s)',
            contentView: 'tools-emissor-de-recibos::pdf.batch',
            data: $result,
            generatedAt: now()->format('d/m/Y H:i'),
            summaryLabel: 'Recibos gerados',
            summaryValue: (string) count($result['receipts']),
            backLabel: 'Voltar à geração em lote',
            printLabel: 'Imprimir / Salvar lote como PDF',
        ));
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

    /** @param array<string, mixed> $result */
    private function renderPdf(array $result, BrowserPrintExporter $exporter): View
    {
        $receipt = $result['details']['receipt'] ?? null;
        if (! is_array($receipt)) {
            throw ValidationException::withMessages(['receipt' => 'Não foi possível preparar o recibo para exportação.']);
        }

        return $exporter->render(new PrintableDocument(
            title: 'Recibo nº '.$receipt['number'],
            subtitle: 'Documento emitido pelo Emissor de Recibos',
            contentView: 'tools-emissor-de-recibos::pdf.receipt',
            data: ['receipt' => $receipt],
            generatedAt: now()->format('d/m/Y H:i'),
            summaryLabel: 'Valor recebido',
            summaryValue: (string) $receipt['amount'],
            backLabel: 'Voltar ao recibo',
            printLabel: 'Imprimir / Salvar como PDF',
        ));
    }
}
