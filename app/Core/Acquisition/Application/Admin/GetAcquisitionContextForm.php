<?php

namespace App\Core\Acquisition\Application\Admin;

use App\Core\Acquisition\Contracts\AcquisitionContextAdministration;

final readonly class GetAcquisitionContextForm
{
    public function __construct(private AcquisitionContextAdministration $contexts) {}

    /** @return array{context:array<string,mixed>|null,tools:list<array{slug:string,name:string}>,articles:list<array{slug:string,title:string}>} */
    public function execute(?int $id = null): array
    {
        return [
            'context' => $id === null ? null : $this->contexts->find($id),
            'tools' => $this->contexts->toolOptions(),
            'articles' => $this->contexts->articleOptions(),
        ];
    }
}
