<?php

declare(strict_types=1);

namespace App\Tools\ContractGenerator\Presentation\Controllers;

use App\Core\Export\Contracts\PdfExporter;
use App\Core\Export\Contracts\SpreadsheetExporter;
use App\Core\Export\Data\PdfDocument;
use App\Core\Export\Services\StructuredResultExportFactory;
use App\Tools\ContractGenerator\Application\Actions\BuildContractDraft;
use App\Tools\ContractGenerator\Domain\Enums\ContractType;
use App\Tools\ContractGenerator\Domain\Enums\PartyDocumentType;
use App\Tools\ContractGenerator\Domain\Services\ContractTextGenerator;
use App\Tools\ContractGenerator\Infrastructure\Export\ContractDocxExporter;
use App\Tools\ContractGenerator\Presentation\Requests\BuildContractDraftRequest;
use App\Tools\ContractGenerator\Presentation\Requests\PreviewContractTextRequest;
use App\Tools\ContractGenerator\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\View\View;

final class ContractGeneratorController
{
    public function index(Request $request): View
    {
        $selectedType = ContractType::tryFrom((string) $request->query('tipo'));

        return $this->render($selectedType);
    }

    public function build(
        BuildContractDraftRequest $request,
        BuildContractDraft $buildDraft,
        ContractTextGenerator $textGenerator
    ): View {
        $draft = $buildDraft->execute($request->validated());
        $contractText = $textGenerator->generate($draft);

        return $this->render($draft->type, $draft->toArray(), $contractText->toArray());
    }

    public function preview(PreviewContractTextRequest $request): View
    {
        $validated = $request->validated();
        $selectedType = ContractType::from((string) $validated['contract_type']);

        return $this->render(
            selectedType: $selectedType,
            contractText: [
                'title' => 'Contrato editado',
                'content' => (string) $validated['contract_text'],
            ],
            edited: true,
        );
    }

    public function exportPdf(
        PreviewContractTextRequest $request,
        PdfExporter $exporter,
    ): Response {
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

    public function exportDocx(
        PreviewContractTextRequest $request,
        ContractDocxExporter $exporter,
    ): Response {
        $validated = $request->validated();
        $type = ContractType::from((string) $validated['contract_type']);

        return $exporter->download(
            $type->documentTitle(),
            (string) $validated['contract_text'],
        );
    }

    /**
     * @param array<string, mixed>|null $draft
     * @param array{title: string, content: string}|null $contractText
     */
    private function render(
        ?ContractType $selectedType,
        ?array $draft = null,
        ?array $contractText = null,
        bool $edited = false,
    ): View {
        return view('tools-gerador-de-contratos::index', [
            'selectedType' => $selectedType,
            'contractTypes' => ContractType::cases(),
            'documentTypes' => PartyDocumentType::options(),
            'draft' => $draft,
            'contractText' => $contractText,
            'edited' => $edited,
        ]);
    }
}
