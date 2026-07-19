<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Tools\History;

use App\Core\Dates\ReferenceDate;
use App\Core\Tools\History\Contracts\ToolRunFavorites;
use App\Core\Tools\History\Contracts\ToolRunHistory;
use App\Core\Tools\History\Data\RuleVersion;
use App\Core\Tools\History\Data\ToolRunHistoryQuery;
use App\Models\User;
use App\Tools\SimplesNacionalCalculator\Tool;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ToolRunHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_paginates_only_owned_succeeded_runs_and_exposes_favorite_state(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $history = app(ToolRunHistory::class);
        $favorites = app(ToolRunFavorites::class);
        $tool = new Tool;

        $older = $history->recordSucceeded(
            $tool,
            new RuleVersion(Tool::HISTORY_RULE_VERSION),
            ReferenceDate::fromString('2026-06-01'),
            ['reference_month' => '2026-06'],
            ['estimated_das' => 'R$ 500,00'],
            (int) $owner->getAuthIdentifier(),
        );

        $newer = $history->recordSucceeded(
            $tool,
            new RuleVersion(Tool::HISTORY_RULE_VERSION),
            ReferenceDate::fromString('2026-07-01'),
            ['reference_month' => '2026-07'],
            ['estimated_das' => 'R$ 600,00'],
            (int) $owner->getAuthIdentifier(),
        );

        $history->recordSucceeded(
            $tool,
            new RuleVersion(Tool::HISTORY_RULE_VERSION),
            ReferenceDate::fromString('2026-07-01'),
            ['reference_month' => '2026-07'],
            ['estimated_das' => 'R$ 700,00'],
            (int) $other->getAuthIdentifier(),
        );

        $favorites->favoriteOwned(Tool::SLUG, $newer->id, (int) $owner->getAuthIdentifier());

        $page = $history->paginateSucceeded(new ToolRunHistoryQuery(
            toolSlug: Tool::SLUG,
            userId: (int) $owner->getAuthIdentifier(),
            page: 1,
            perPage: 1,
        ));

        self::assertSame(2, $page->total);
        self::assertSame(2, $page->lastPage);
        self::assertTrue($page->hasMorePages());
        self::assertCount(1, $page->items);
        self::assertSame($newer->id, $page->items[0]->id);
        self::assertTrue($page->items[0]->favorite);

        $favoritesOnly = $history->paginateSucceeded(new ToolRunHistoryQuery(
            toolSlug: Tool::SLUG,
            userId: (int) $owner->getAuthIdentifier(),
            favoritesOnly: true,
        ));

        self::assertSame(1, $favoritesOnly->total);
        self::assertSame($newer->id, $favoritesOnly->items[0]->id);
        self::assertNotSame($older->id, $favoritesOnly->items[0]->id);
    }

    public function test_it_never_exposes_or_favorites_another_users_run(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $history = app(ToolRunHistory::class);
        $favorites = app(ToolRunFavorites::class);

        $entry = $history->recordSucceeded(
            new Tool,
            new RuleVersion(Tool::HISTORY_RULE_VERSION),
            ReferenceDate::fromString('2026-07-01'),
            ['reference_month' => '2026-07'],
            ['estimated_das' => 'R$ 600,00'],
            (int) $owner->getAuthIdentifier(),
        );

        $this->expectException(ModelNotFoundException::class);

        $favorites->favoriteOwned(
            Tool::SLUG,
            $entry->id,
            (int) $other->getAuthIdentifier(),
        );
    }

    public function test_toggle_updates_the_entry_without_exposing_the_model(): void
    {
        $user = User::factory()->create();
        $history = app(ToolRunHistory::class);
        $favorites = app(ToolRunFavorites::class);

        $entry = $history->recordSucceeded(
            new Tool,
            new RuleVersion(Tool::HISTORY_RULE_VERSION),
            ReferenceDate::fromString('2026-07-01'),
            ['reference_month' => '2026-07'],
            ['estimated_das' => 'R$ 600,00'],
            (int) $user->getAuthIdentifier(),
        );

        self::assertTrue($favorites->toggleOwned(Tool::SLUG, $entry->id, (int) $user->getAuthIdentifier()));
        self::assertTrue($history->findSucceededOwned(Tool::SLUG, $entry->id, (int) $user->getAuthIdentifier())->favorite);

        self::assertFalse($favorites->toggleOwned(Tool::SLUG, $entry->id, (int) $user->getAuthIdentifier()));
        self::assertFalse($history->findSucceededOwned(Tool::SLUG, $entry->id, (int) $user->getAuthIdentifier())->favorite);
    }
}
