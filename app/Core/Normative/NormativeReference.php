<?php

declare(strict_types=1);

namespace App\Core\Normative;

use App\Core\Dates\EffectivePeriod;
use App\Core\Dates\ReferenceDate;
use App\Core\Exceptions\InvalidValue;

final readonly class NormativeReference
{
    public function __construct(
        public NormativeSourceType $type,
        public string $identifier,
        public string $title,
        public ReferenceDate $publishedAt,
        public ?EffectivePeriod $effectivePeriod = null,
        public ?string $officialUrl = null,
        public ?string $article = null,
    ) {
        if (trim($identifier) === '') {
            throw new InvalidValue('A referência normativa precisa de um identificador.');
        }

        if (trim($title) === '') {
            throw new InvalidValue('A referência normativa precisa de um título.');
        }

        if ($officialUrl !== null && filter_var($officialUrl, FILTER_VALIDATE_URL) === false) {
            throw new InvalidValue('A URL da referência normativa é inválida.');
        }
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'type' => $this->type->value,
            'identifier' => $this->identifier,
            'title' => $this->title,
            'published_at' => $this->publishedAt->toString(),
            'effective_from' => $this->effectivePeriod?->startsAt->toString(),
            'effective_until' => $this->effectivePeriod?->endsAt?->toString(),
            'official_url' => $this->officialUrl,
            'article' => $this->article,
        ];
    }

    public function isEffectiveOn(ReferenceDate $date): bool
    {
        return $this->effectivePeriod?->contains($date) ?? ! $date->isBefore($this->publishedAt);
    }
}
