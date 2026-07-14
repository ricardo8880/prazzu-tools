<?php

declare(strict_types=1);

namespace App\Core\Imports\Contracts;

use App\Core\Imports\Data\TabularDataset;
use Illuminate\Http\UploadedFile;

interface TabularFileReader
{
    public function supports(string $extension): bool;

    public function read(UploadedFile $file, int $maximumRows): TabularDataset;
}
