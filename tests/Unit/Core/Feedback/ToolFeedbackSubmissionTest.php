<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Feedback;

use App\Core\Feedback\Data\ToolFeedbackSubmission;
use App\Core\Feedback\Enums\ToolFeedbackType;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ToolFeedbackSubmissionTest extends TestCase
{
    public function test_it_normalizes_optional_text_without_losing_the_original_context(): void
    {
        $submission = new ToolFeedbackSubmission(
            toolSlug: 'gerador-de-contratos',
            type: ToolFeedbackType::Problem,
            message: '  O contrato não foi gerado.  ',
            attemptedAction: '  Gerar um contrato de prestação de serviços.  ',
            path: '/ferramentas/gerador-de-contratos',
            url: 'https://tools.prazzu.com.br/ferramentas/gerador-de-contratos',
            context: ['mode' => 'prestacao-de-servicos'],
        );

        self::assertSame('O contrato não foi gerado.', $submission->normalizedMessage());
        self::assertSame('Gerar um contrato de prestação de serviços.', $submission->normalizedAttemptedAction());
        self::assertSame(['mode' => 'prestacao-de-servicos'], $submission->context);
    }

    public function test_message_is_required(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('A mensagem do feedback é obrigatória.');

        new ToolFeedbackSubmission(
            toolSlug: 'gerador-de-contratos',
            type: ToolFeedbackType::Suggestion,
            message: '   ',
            attemptedAction: null,
            path: '/ferramentas/gerador-de-contratos',
            url: 'https://tools.prazzu.com.br/ferramentas/gerador-de-contratos',
        );
    }

    public function test_path_and_url_must_be_valid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('O caminho da página é inválido.');

        new ToolFeedbackSubmission(
            toolSlug: 'gerador-de-contratos',
            type: ToolFeedbackType::Other,
            message: 'Mensagem válida.',
            attemptedAction: null,
            path: 'ferramentas/gerador-de-contratos',
            url: 'https://tools.prazzu.com.br/ferramentas/gerador-de-contratos',
        );
    }
}
