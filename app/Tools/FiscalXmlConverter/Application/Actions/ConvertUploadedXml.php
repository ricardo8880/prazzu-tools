<?php

declare(strict_types=1);

namespace App\Tools\FiscalXmlConverter\Application\Actions;

use App\Tools\FiscalXmlConverter\Domain\Data\FiscalDocument;
use App\Tools\FiscalXmlConverter\Domain\Exceptions\InvalidFiscalXml;
use App\Tools\FiscalXmlConverter\Domain\Services\NfeXmlParser;
use Illuminate\Http\UploadedFile;
use RuntimeException;

final readonly class ConvertUploadedXml
{
    public function __construct(private NfeXmlParser $parser) {}

    public function execute(UploadedFile $file): FiscalDocument
    {
        $path = $file->getRealPath();
        if ($path === false || ! is_readable($path)) {
            throw new InvalidFiscalXml('Não foi possível ler o arquivo enviado.');
        }

        $contents = file_get_contents($path);
        if ($contents === false) {
            throw new RuntimeException('Não foi possível carregar o conteúdo do XML.');
        }

        return $this->parser->parse($contents);
    }
}
