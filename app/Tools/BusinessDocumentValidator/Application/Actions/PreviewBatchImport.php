<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Application\Actions;

use App\Core\Imports\Contracts\ImportDatasetStore;
use App\Core\Imports\Services\CompositeTabularFileReader;
use Illuminate\Http\UploadedFile;

final readonly class PreviewBatchImport
{
    private const MAXIMUM_ROWS = 500;

    public function __construct(
        private CompositeTabularFileReader $reader,
        private ImportDatasetStore $store,
    ) {}

    public function execute(UploadedFile $file, string $ownerKey): array
    {
        $dataset = $this->reader->read($file, self::MAXIMUM_ROWS);
        $token = $this->store->put($dataset, $ownerKey);

        return [
            'token' => $token,
            'file_name' => $dataset->originalName,
            'format' => strtoupper($dataset->format),
            'headers' => $dataset->headers,
            'preview_rows' => array_slice($dataset->rows, 0, 8),
            'total_rows' => count($dataset->rows),
            'maximum_rows' => self::MAXIMUM_ROWS,
            'suggested_mapping' => $this->suggestMapping($dataset->headers),
        ];
    }

    /** @param list<string> $headers */
    private function suggestMapping(array $headers): array
    {
        $suggestions = [];
        $patterns = [
            'document_column' => ['cnpj', 'cpf', 'documento', 'document'],
            'legal_name_column' => ['razao social', 'razão social', 'nome empresarial'],
            'trade_name_column' => ['nome fantasia', 'fantasia'],
            'state_column' => ['uf', 'estado'],
            'city_column' => ['municipio', 'município', 'cidade'],
            'state_registration_column' => ['inscricao estadual', 'inscrição estadual', 'ie'],
        ];

        foreach ($patterns as $field => $aliases) {
            foreach ($headers as $header) {
                $normalized = $this->normalize($header);
                if (in_array($normalized, array_map($this->normalize(...), $aliases), true)) {
                    $suggestions[$field] = $header;
                    break;
                }
            }
        }

        return $suggestions;
    }

    private function normalize(string $value): string
    {
        $value = mb_strtolower(trim($value));
        $transliterated = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);

        return preg_replace('/[^a-z0-9]+/', ' ', $transliterated ?: $value) ?: $value;
    }
}
