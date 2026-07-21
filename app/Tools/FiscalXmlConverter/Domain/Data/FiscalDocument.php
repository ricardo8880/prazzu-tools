<?php

declare(strict_types=1);

namespace App\Tools\FiscalXmlConverter\Domain\Data;

final readonly class FiscalDocument
{
    /** @param list<FiscalItem> $items */
    public function __construct(
        public string $model,
        public string $accessKey,
        public string $number,
        public string $series,
        public ?string $issuedAt,
        public FiscalParty $issuer,
        public FiscalParty $recipient,
        public array $totals,
        public array $items,
        public array $warnings = [],
    ) {}

    public function toArray(): array
    {
        return [
            'model' => $this->model, 'access_key' => $this->accessKey, 'number' => $this->number,
            'series' => $this->series, 'issued_at' => $this->issuedAt,
            'issuer' => $this->issuer->toArray(), 'recipient' => $this->recipient->toArray(),
            'totals' => $this->totals, 'items' => array_map(static fn (FiscalItem $item) => $item->toArray(), $this->items),
            'warnings' => $this->warnings,
        ];
    }
}
