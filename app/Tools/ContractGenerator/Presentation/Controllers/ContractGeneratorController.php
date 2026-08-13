<?php

declare(strict_types=1);

namespace App\Tools\ContractGenerator\Presentation\Controllers;

use App\Core\Access\Services\ToolFeatureRequestAuthorizer;
use App\Core\Access\Services\ToolPersistenceAuthorizer;
use App\Core\Dates\ReferenceDate;
use App\Core\Export\Contracts\PdfExporter;
use App\Core\Export\Contracts\SpreadsheetExporter;
use App\Core\Export\Data\PdfDocument;
use App\Core\Export\Services\StructuredResultExportFactory;
use App\Core\ToolProfiles\Services\ToolProfileManager;
use App\Core\Tools\History\Contracts\ToolRunRecorder;
use App\Core\Tools\History\Data\RuleVersion;
use App\Tools\ContractGenerator\Application\Actions\BuildContractDraft;
use App\Tools\ContractGenerator\Application\Actions\ManageContractHistory;
use App\Tools\ContractGenerator\Domain\Enums\ContractTemplate;
use App\Tools\ContractGenerator\Domain\Enums\ContractType;
use App\Tools\ContractGenerator\Domain\Enums\PartyDocumentType;
use App\Tools\ContractGenerator\Domain\Enums\SmartClause;
use App\Tools\ContractGenerator\Domain\Services\ContractTextGenerator;
use App\Tools\ContractGenerator\Infrastructure\Export\ContractDocxExporter;
use App\Tools\ContractGenerator\Presentation\Requests\BuildContractDraftRequest;
use App\Tools\ContractGenerator\Presentation\Requests\PreviewContractTextRequest;
use App\Tools\ContractGenerator\Tool;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

final class ContractGeneratorController
{
    public function index(
        Request $request,
        ToolFeatureRequestAuthorizer $features,
        Tool $module,
        ToolProfileManager $profiles,
        ManageContractHistory $history,
    ): View {
        $template = ContractTemplate::tryFrom((string) $request->query('modelo'));
        $selectedType = $template?->contractType() ?? ContractType::tryFrom((string) $request->query('tipo'));

        if ($template?->isPlus()) {
            $features->require($module, 'contract_library', $request);
            if ($template->presetClauses() !== []) {
                $features->require($module, 'smart_clauses', $request);
            }
        }

        return $this->render($request, $features, $module, $profiles, $history, $selectedType, selectedTemplate: $template);
    }

    public function build(
        BuildContractDraftRequest $request,
        BuildContractDraft $buildDraft,
        ContractTextGenerator $textGenerator,
        ToolFeatureRequestAuthorizer $features,
        ToolPersistenceAuthorizer $persistence,
        ToolRunRecorder $recorder,
        Tool $module,
        ToolProfileManager $profiles,
        ManageContractHistory $history,
    ): View {
        $validated = $request->validated();
        $template = isset($validated['template_key']) ? ContractTemplate::tryFrom((string) $validated['template_key']) : null;
        $features->requireIf($template?->isPlus() === true, $module, 'contract_library', $request);
        $features->requireIf(((array) ($validated['smart_clauses'] ?? [])) !== [] || ($template?->presetClauses() ?? []) !== [], $module, 'smart_clauses', $request);

        $draft = $buildDraft->execute($validated);
        $contractText = $textGenerator->generate($draft);
        $runId = null;

        if ($persistence->allowsHistory($module, $request->user())) {
            $run = $recorder->start(
                $module,
                new RuleVersion($module->manifest()->version),
                ReferenceDate::fromString($draft->signingDate->format('Y-m-d')),
                $validated,
                (int) $request->user()->getAuthIdentifier(),
            );
            $recorder->succeed($run, [
                'contract_text' => $contractText->content,
                'contract_title' => $contractText->title,
                'template' => $draft->template->value,
                'draft' => $draft->toArray(),
            ]);
            $runId = $run->id;
        }

        return $this->render(
            $request,
            $features,
            $module,
            $profiles,
            $history,
            $draft->type,
            $draft->toArray(),
            $contractText->toArray(),
            selectedTemplate: $draft->template,
            currentRunId: $runId,
        );
    }

    public function preview(
        PreviewContractTextRequest $request,
        ToolFeatureRequestAuthorizer $features,
        Tool $module,
        ToolProfileManager $profiles,
        ManageContractHistory $history,
    ): View {
        $validated = $request->validated();
        $selectedType = ContractType::from((string) $validated['contract_type']);

        return $this->render(
            $request,
            $features,
            $module,
            $profiles,
            $history,
            $selectedType,
            contractText: ['title' => 'Contrato editado', 'content' => (string) $validated['contract_text']],
            edited: true,
        );
    }

    public function saveVersion(
        PreviewContractTextRequest $request,
        ToolRunRecorder $recorder,
        Tool $module,
    ): RedirectResponse {
        $validated = $request->validated();
        $type = ContractType::from((string) $validated['contract_type']);
        $run = $recorder->start(
            $module,
            new RuleVersion($module->manifest()->version),
            ReferenceDate::fromString(now()->format('Y-m-d')),
            ['contract_type' => $type->value, 'source_run_id' => $request->string('source_run_id')->toString()],
            (int) $request->user()->getAuthIdentifier(),
        );
        $recorder->succeed($run, [
            'contract_text' => (string) $validated['contract_text'],
            'contract_title' => $type->documentTitle(),
            'saved_version' => true,
        ]);

        return redirect()->route('tools.gerador-de-contratos.history.index')
            ->with('history_message', 'Nova versão salva no histórico.');
    }

    public function history(Request $request, ManageContractHistory $history): View
    {
        return view('tools-gerador-de-contratos::history.index', [
            'runs' => $history->paginate((int) $request->user()->getAuthIdentifier(), max(1, $request->integer('page', 1))),
        ]);
    }

    public function toggleFavorite(Request $request, string $run, ManageContractHistory $history): RedirectResponse
    {
        $favorite = $history->toggleFavorite($run, (int) $request->user()->getAuthIdentifier());

        return back()->with('history_message', $favorite ? 'Contrato adicionado aos favoritos.' : 'Contrato removido dos favoritos.');
    }

    public function destroyHistory(Request $request, string $run, ManageContractHistory $history): RedirectResponse
    {
        $history->delete($run, (int) $request->user()->getAuthIdentifier());

        return back()->with('history_message', 'Versão removida do histórico.');
    }

    public function compareVersions(Request $request, ManageContractHistory $history): View
    {
        $validated = $request->validate([
            'left' => ['required', 'uuid', 'different:right'],
            'right' => ['required', 'uuid', 'different:left'],
        ]);

        return view('tools-gerador-de-contratos::history.compare', $history->compare(
            (string) $validated['left'],
            (string) $validated['right'],
            (int) $request->user()->getAuthIdentifier(),
        ));
    }

    public function exportPdf(PreviewContractTextRequest $request, PdfExporter $exporter): Response
    {
        $validated = $request->validated();
        $type = ContractType::from((string) $validated['contract_type']);
        Log::info('Contract PDF download requested.', [
            'tool' => Tool::SLUG,
            'contract_type' => $type->value,
            'content_length' => mb_strlen((string) $validated['contract_text']),
            'user_id' => $request->user()?->getAuthIdentifier(),
        ]);

        try {
            return $exporter->download(new PdfDocument(
                filename: 'contrato-'.now()->format('Y-m-d'),
                view: 'exports.printable-document',
                data: [
                    'title' => $type->documentTitle(),
                    'contentView' => 'tools-gerador-de-contratos::pdf.contract',
                    'contentData' => ['content' => (string) $validated['contract_text']],
                    'generatedAt' => now()->format('d/m/Y H:i'),
                ],
            ));
        } catch (\Throwable $exception) {
            Log::error('Contract PDF download failed.', [
                'tool' => Tool::SLUG,
                'contract_type' => $type->value,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);
            throw $exception;
        }
    }

    public function exportXlsx(PreviewContractTextRequest $request, SpreadsheetExporter $exporter, StructuredResultExportFactory $documents): Response
    {
        $validated = $request->validated();
        $type = ContractType::from((string) $validated['contract_type']);

        return $exporter->download($documents->spreadsheet('contrato-'.now()->format('Y-m-d'), [
            'titulo' => $type->documentTitle(),
            'conteudo' => (string) $validated['contract_text'],
        ], ['tipo' => $type->value]));
    }

    public function exportDocx(PreviewContractTextRequest $request, ContractDocxExporter $exporter): Response
    {
        $validated = $request->validated();
        $type = ContractType::from((string) $validated['contract_type']);

        return $exporter->download($type->documentTitle(), (string) $validated['contract_text']);
    }

    /** @param array<string, mixed>|null $draft @param array{title: string, content: string}|null $contractText */
    private function render(
        Request $request,
        ToolFeatureRequestAuthorizer $features,
        Tool $module,
        ToolProfileManager $profiles,
        ManageContractHistory $history,
        ?ContractType $selectedType,
        ?array $draft = null,
        ?array $contractText = null,
        bool $edited = false,
        ?ContractTemplate $selectedTemplate = null,
        ?string $currentRunId = null,
    ): View {
        $user = $request->user();
        $featureAccess = [];
        foreach (['contract_library', 'smart_clauses', 'favorites', 'company_autofill', 'history', 'version_comparison'] as $key) {
            $featureAccess[$key] = $features->allows($module, $key, $request);
        }

        $companyProfiles = collect();
        $companyAutofill = null;
        if ($user !== null && $featureAccess['company_autofill']) {
            $companyProfiles = $profiles->companies((int) $user->getAuthIdentifier());
            $companyId = $request->integer('empresa');
            if ($companyId > 0) {
                $profile = $profiles->findCompanyOwned($companyId, (int) $user->getAuthIdentifier());
                $companyAutofill = [
                    'name' => $profile->legal_name ?: $profile->name,
                    'document_type' => filled($profile->document) ? 'cnpj' : null,
                    'document' => $profile->document,
                ];
            }
        }

        $recentHistory = $user !== null && $featureAccess['history']
            ? $history->recent((int) $user->getAuthIdentifier())
            : [];

        return view('tools-gerador-de-contratos::index', [
            'selectedType' => $selectedType,
            'selectedTemplate' => $selectedTemplate ?? ($selectedType ? ContractTemplate::essentialFor($selectedType) : null),
            'contractTypes' => ContractType::cases(),
            'contractTemplates' => ContractTemplate::cases(),
            'smartClauseOptions' => SmartClause::cases(),
            'documentTypes' => PartyDocumentType::options(),
            'featureAccess' => $featureAccess,
            'companyProfiles' => $companyProfiles,
            'companyAutofill' => $companyAutofill,
            'recentHistory' => $recentHistory,
            'draft' => $draft,
            'contractText' => $contractText,
            'edited' => $edited,
            'currentRunId' => $currentRunId,
        ]);
    }
}
