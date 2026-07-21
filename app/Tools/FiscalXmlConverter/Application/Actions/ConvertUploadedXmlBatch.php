<?php

declare(strict_types=1);

namespace App\Tools\FiscalXmlConverter\Application\Actions;

use App\Core\Money\Money;
use App\Tools\FiscalXmlConverter\Domain\Exceptions\InvalidFiscalXml;
use App\Tools\FiscalXmlConverter\Domain\Services\NfeXmlParser;
use Illuminate\Http\UploadedFile;

final readonly class ConvertUploadedXmlBatch
{
    public function __construct(private NfeXmlParser $parser) {}

    /** @param list<UploadedFile> $files @return array{documents:list<array<string,mixed>>,errors:list<array{file:string,message:string}>,summary:array<string,mixed>} */
    public function execute(array $files): array
    {
        $documents = [];
        $errors = [];
        $totalItems = 0;
        $documentTotal = Money::zero();

        foreach ($files as $file) {
            try {
                $path = $file->getRealPath();
                if ($path === false || ! is_readable($path)) {
                    throw new InvalidFiscalXml('Não foi possível ler o arquivo enviado.');
                }
                $contents = file_get_contents($path);
                if ($contents === false) {
                    throw new InvalidFiscalXml('Não foi possível carregar o conteúdo do XML.');
                }
                $document = $this->parser->parse($contents)->toArray();
                $document['source_file'] = $file->getClientOriginalName();
                $documents[] = $document;
                $totalItems += count($document['items'] ?? []);
                $documentTotal = $documentTotal->add(Money::fromDecimal((string) ($document['totals']['document'] ?? '0')));
            } catch (InvalidFiscalXml $exception) {
                $errors[] = ['file' => $file->getClientOriginalName(), 'message' => $exception->getMessage()];
            }
        }

        return [
            'documents' => $documents,
            'errors' => $errors,
            'summary' => [
                'received' => count($files),
                'processed' => count($documents),
                'failed' => count($errors),
                'items' => $totalItems,
                'document_total' => sprintf('%d.%02d', intdiv($documentTotal->minorAmount(), 100), abs($documentTotal->minorAmount() % 100)),
            ],
        ];
    }
}
