<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Feedback;

use App\Core\Feedback\Data\ToolResolutionSubmission;
use App\Core\Feedback\Enums\ToolResolution;
use App\Core\Feedback\Enums\ToolResolutionReason;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ToolResolutionSubmissionTest extends TestCase
{
    public function test_partial_resolution_requires_a_reason_and_normalizes_comment(): void
    {
        $submission = new ToolResolutionSubmission(
            toolSlug: 'gerador-de-contratos',
            resolution: ToolResolution::Partially,
            reason: ToolResolutionReason::MissingOption,
            comment: '  Preciso de mais uma opção.  ',
            path: '/ferramentas/gerador-de-contratos',
            url: 'https://tools.prazzu.com.br/ferramentas/gerador-de-contratos',
        );

        self::assertSame('Preciso de mais uma opção.', $submission->normalizedComment());
    }

    public function test_partial_resolution_without_reason_is_invalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('O motivo é obrigatório');

        new ToolResolutionSubmission(
            toolSlug: 'gerador-de-contratos',
            resolution: ToolResolution::Partially,
            reason: null,
            comment: null,
            path: '/ferramentas/gerador-de-contratos',
            url: 'https://tools.prazzu.com.br/ferramentas/gerador-de-contratos',
        );
    }

    public function test_positive_resolution_cannot_keep_an_incomplete_reason(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Uma resposta positiva não deve registrar motivo');

        new ToolResolutionSubmission(
            toolSlug: 'gerador-de-contratos',
            resolution: ToolResolution::Yes,
            reason: ToolResolutionReason::Other,
            comment: null,
            path: '/ferramentas/gerador-de-contratos',
            url: 'https://tools.prazzu.com.br/ferramentas/gerador-de-contratos',
        );
    }
}
