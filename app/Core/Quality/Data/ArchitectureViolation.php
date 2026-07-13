<?php

namespace App\Core\Quality\Data;

final readonly class ArchitectureViolation
{
    public function __construct(
        public string $rule,
        public string $file,
        public int $line,
        public string $message,
    ) {}

    public function format(): string
    {
        return sprintf('%s:%d [%s] %s', $this->file, $this->line, $this->rule, $this->message);
    }
}
