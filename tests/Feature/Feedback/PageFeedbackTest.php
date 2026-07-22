<?php

namespace Tests\Feature\Feedback;

use App\Core\Feedback\Models\PageFeedback;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Analytics\Concerns\ActsAsInternalAdministrator;
use Tests\TestCase;

final class PageFeedbackTest extends TestCase
{
    use ActsAsInternalAdministrator, RefreshDatabase;

    public function test_global_layout_displays_page_feedback_control(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Avaliar página');
    }

    public function test_admin_pages_do_not_display_page_feedback_control(): void
    {
        $this->signInAsInternalAdministrator();

        $this->get(route('admin.index'))
            ->assertOk()
            ->assertDontSee('Avaliar página');
    }

    public function test_feedback_uses_the_canonical_page_url_without_query_parameters(): void
    {
        $this->get('/?utm_source=test')
            ->assertOk()
            ->assertSee('name="path" value="/"', false)
            ->assertSee('name="url" value="http://localhost"', false)
            ->assertDontSee('utm_source', false);
    }

    public function test_visitor_can_submit_page_feedback(): void
    {
        $this->postJson(route('feedback.page.store'), [
            'rating' => 5,
            'comment' => 'Página clara e útil.',
            'path' => '/ferramentas',
            'url' => 'http://localhost/ferramentas',
            'page_title' => 'Ferramentas',
        ])->assertCreated()
            ->assertJsonPath('message', 'Obrigado! Sua avaliação foi enviada.');

        $feedback = PageFeedback::query()->firstOrFail();

        self::assertSame(5, $feedback->rating);
        self::assertSame('Página clara e útil.', $feedback->comment);
        self::assertSame('/ferramentas', $feedback->path);
    }

    public function test_path_must_fit_the_database_column(): void
    {
        $this->postJson(route('feedback.page.store'), [
            'rating' => 5,
            'path' => '/'.str_repeat('a', 512),
            'url' => 'http://localhost/',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('path');
    }

    public function test_rating_must_be_between_one_and_five(): void
    {
        $this->postJson(route('feedback.page.store'), [
            'rating' => 6,
            'path' => '/',
            'url' => 'http://localhost/',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('rating');
    }
}
