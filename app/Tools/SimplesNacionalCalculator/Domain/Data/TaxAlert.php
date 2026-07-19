<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Domain\Data;

final readonly class TaxAlert
{
    public function __construct(
        public string $level,
        public string $title,
        public string $message,
    ) {}

    /** @return array{level:string,title:string,message:string} */
    public function toArray(): array
    {
        return [
            'level' => $this->level,
            'title' => $this->title,
            'message' => $this->message,
        ];
    }
}
