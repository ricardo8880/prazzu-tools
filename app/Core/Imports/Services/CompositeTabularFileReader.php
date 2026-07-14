<?php

declare(strict_types=1);

namespace App\Core\Imports\Services;

use App\Core\Imports\Contracts\TabularFileReader;
use App\Core\Imports\Data\TabularDataset;
use App\Core\Imports\Exceptions\UnsupportedImportFormat;
use Illuminate\Http\UploadedFile;

final readonly class CompositeTabularFileReader
{
    /** @param iterable<TabularFileReader> $readers */
    public function __construct(private iterable $readers) {}

    public function read(UploadedFile $file, int $maximumRows): TabularDataset
    {
        $extension = strtolower($file->getClientOriginalExtension());

        foreach ($this->readers as $reader) {
            if ($reader->supports($extension)) {
                return $reader->read($file, $maximumRows);
            }
        }

        throw new UnsupportedImportFormat('Formato não suportado. Envie um arquivo CSV ou XLSX.');
    }
}
