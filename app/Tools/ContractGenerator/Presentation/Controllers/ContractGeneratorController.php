<?php

declare(strict_types=1);

namespace App\Tools\ContractGenerator\Presentation\Controllers;

use App\Core\Analytics\Contracts\PlatformAnalytics;
use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use App\Core\Analytics\Domain\Events\AnalyticsEvent;
use App\Core\Export\Data\PrintableDocument;
use App\Core\Export\Services\BrowserPrintExporter;
use App\Tools\ContractGenerator\Application\Actions\BuildContractDraft;
use App\Tools\ContractGenerator\Domain\Enums\ContractType;
use App\Tools\ContractGenerator\Domain\Enums\PartyDocumentType;
use App\Tools\ContractGenerator\Domain\Services\ContractTextGenerator;
use App\Tools\ContractGenerator\Infrastructure\Export\ContractDocxExporter;
use App\Tools\ContractGenerator\Presentation\Requests\BuildContractDraftRequest;
use App\Tools\ContractGenerator\Presentation\Requests\PreviewContractTextRequest;
use App\Tools\ContractGenerator\Tool;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
        ContractTextGenerator $textGenerator,
        PlatformAnalytics $analytics,
    ): View {
        $draft = $buildDraft->execute($request->validated());
        $contractText = $textGenerator->generate($draft);

        $analytics->track(AnalyticsEvent::make(
            AnalyticsEventName::ToolCalculationCompleted->value,
            'tool',
            ['subject_type' => 'tool', 'subject_slug' => Tool::SLUG, 'contract_type' => $draft->type->value],
        ), $request);

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
        BrowserPrintExporter $exporter,
        PlatformAnalytics $analytics,
    ): View {
        $validated = $request->validated();
        $type = ContractType::from((string) $validated['contract_type']);
        $title = $type->documentTitle();

        $analytics->track(AnalyticsEvent::make(
            AnalyticsEventName::ToolResultExported->value,
            'tool',
            ['subject_type' => 'tool', 'subject_slug' => Tool::SLUG, 'contract_type' => $type->value, 'format' => 'pdf'],
        ), $request);

        return $exporter->render(new PrintableDocument(
            title: 'Contrato para impressão',
            subtitle: $title,
            contentView: 'tools-gerador-de-contratos::pdf.contract',
            data: ['content' => (string) $validated['contract_text']],
            generatedAt: now()->format('d/m/Y H:i'),
            backLabel: 'Voltar ao contrato',
            printLabel: 'Imprimir / Salvar como PDF',
        ));
    }

    public function exportDocx(
        PreviewContractTextRequest $request,
        ContractDocxExporter $exporter,
        PlatformAnalytics $analytics,
    ): Response {
        $validated = $request->validated();
        $type = ContractType::from((string) $validated['contract_type']);

        $analytics->track(AnalyticsEvent::make(
            AnalyticsEventName::ToolResultExported->value,
            'tool',
            ['subject_type' => 'tool', 'subject_slug' => Tool::SLUG, 'contract_type' => $type->value, 'format' => 'docx'],
        ), $request);

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
