<?php

declare(strict_types=1);

namespace App\Core\Quality\Data;

use App\Core\Exceptions\InvalidValue;
use App\Core\Quality\Enums\GoldenCaseKind;

final readonly class GoldenCase
{
    /**
     * @param array<string, mixed> $input
     * @param array<string, mixed> $expected
     * @param list<string> $tags
     */
    public function __construct(
        public string $identifier,
        public string $title,
        public GoldenCaseKind $kind,
        public array $input,
        public array $expected,
        public string $reference,
        public ?string $normativeRuleVersion = null,
        public ?string $roundingPolicy = null,
        public array $tags = [],
    ) {
        if (! preg_match('/^[a-z0-9]+(?:[._-][a-z0-9]+)*$/', $identifier)) {
            throw new InvalidValue('O identificador do caso dourado deve usar letras minúsculas, números, ponto, hífen ou sublinhado.');
        }

        if (trim($title) === '' || trim($reference) === '') {
            throw new InvalidValue('Título e referência do caso dourado são obrigatórios.');
        }

        $this->assertNoFloat($input);
        $this->assertNoFloat($expected);

        foreach ($tags as $tag) {
            if (! is_string($tag) || trim($tag) === '') {
                throw new InvalidValue('As tags do caso dourado devem ser textos não vazios.');
            }
        }
    }

    /** @param array<string, mixed> $values */
    private function assertNoFloat(array $values): void
    {
        array_walk_recursive($values, static function (mixed $value): void {
            if (is_float($value)) {
                throw new InvalidValue('Casos dourados não podem usar float. Valores decimais devem ser representados como string ou inteiro de menor unidade.');
            }
        });
    }
}
