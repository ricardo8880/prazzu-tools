<?php

declare(strict_types=1);

namespace App\Core\Tools\Calculation\Data;

use App\Core\Tools\Calculation\Enums\ToolCalculationWarningLevel;
use InvalidArgumentException;

final readonly class ToolCalculationWarning
{
    public function __construct(
        public string $code,
        public string $message,
        public ToolCalculationWarningLevel $level = ToolCalculationWarningLevel::Warning,
        public ?string $title = null,
    ) {
        if (trim($this->code) === '') {
            throw new InvalidArgumentException('O código do alerta do cálculo não pode ser vazio.');
        }

        if (trim($this->message) === '') {
            throw new InvalidArgumentException('A mensagem do alerta do cálculo não pode ser vazia.');
        }
    }

    /** @return array{code:string,title:?string,message:string,level:string} */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'title' => $this->title,
            'message' => $this->message,
            'level' => $this->level->value,
        ];
    }
}
