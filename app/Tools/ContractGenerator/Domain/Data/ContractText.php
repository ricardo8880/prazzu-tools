<?php

declare(strict_types=1);

namespace App\Tools\ContractGenerator\Domain\Data;

final readonly class ContractText
{
    public function __construct(
        public string $title,
        public string $content,
    ) {}

    /** @return array{title: string, content: string} */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'content' => $this->content,
        ];
    }
}
