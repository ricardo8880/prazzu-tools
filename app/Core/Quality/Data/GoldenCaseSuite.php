<?php

declare(strict_types=1);

namespace App\Core\Quality\Data;

use App\Core\Exceptions\InvalidValue;
use App\Core\Quality\Enums\GoldenCaseKind;

final readonly class GoldenCaseSuite
{
    /** @param list<GoldenCase> $cases */
    public function __construct(
        public string $toolSlug,
        public array $cases,
    ) {
        if (! preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $toolSlug)) {
            throw new InvalidValue('O slug da suíte de casos dourados é inválido.');
        }

        if ($cases === []) {
            throw new InvalidValue('A suíte precisa possuir ao menos um caso dourado.');
        }

        $identifiers = [];

        foreach ($cases as $case) {
            if (! $case instanceof GoldenCase) {
                throw new InvalidValue('A suíte possui um caso dourado inválido.');
            }

            if (isset($identifiers[$case->identifier])) {
                throw new InvalidValue("O caso dourado [{$case->identifier}] está duplicado.");
            }

            $identifiers[$case->identifier] = true;
        }
    }

    public function hasKind(GoldenCaseKind $kind): bool
    {
        foreach ($this->cases as $case) {
            if ($case->kind === $kind) {
                return true;
            }
        }

        return false;
    }
}
