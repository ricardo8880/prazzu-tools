<?php

namespace App\Core\Acquisition\Infrastructure\Persistence;

use App\Blog\Models\BlogPost;
use App\Core\Acquisition\Contracts\AcquisitionContextAdministration;
use App\Core\Acquisition\Domain\Enums\AcquisitionContextStatus;
use App\Core\Acquisition\Domain\Enums\AcquisitionContextToolPlacement;
use App\Core\Acquisition\Infrastructure\Cache\AcquisitionContextCache;
use App\Core\Tools\ToolCatalog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

final readonly class EloquentAcquisitionContextAdministration implements AcquisitionContextAdministration
{
    public function __construct(
        private ToolCatalog $tools,
        private AcquisitionContextCache $cache,
    ) {}

    public function paginate(?string $search, ?string $status, int $perPage = 15): LengthAwarePaginator
    {
        $search = trim((string) $search);

        $paginator = AcquisitionContextRecord::query()
            ->withCount(['tools', 'articles'])
            ->when($search !== '', static function ($query) use ($search): void {
                $query->where(static function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('keyword', 'like', "%{$search}%")
                        ->orWhere('campaign_identifier', 'like', "%{$search}%");
                });
            })
            ->when(in_array($status, array_column(AcquisitionContextStatus::cases(), 'value'), true),
                static fn ($query) => $query->where('status', $status))
            ->latest('updated_at')
            ->paginate($perPage)
            ->withQueryString();

        $paginator->through(fn (AcquisitionContextRecord $record): array => [
            'id' => (int) $record->getKey(),
            'name' => $record->name,
            'keyword' => $record->keyword,
            'campaign_identifier' => $record->campaign_identifier,
            'campaign_source' => $record->campaign_source,
            'campaign_medium' => $record->campaign_medium,
            'content_identifier' => $record->content_identifier,
            'video_identifier' => $record->video_identifier,
            'banner_identifier' => $record->banner_identifier,
            'cta_identifier' => $record->cta_identifier,
            'monthly_investment' => $this->moneyInput($record->monthly_investment_cents),
            'investment_currency' => $record->investment_currency ?: 'BRL',
            'status' => $record->status->value,
            'primary_tool_slug' => $record->primary_tool_slug,
            'tools_count' => (int) $record->tools_count,
            'articles_count' => (int) $record->articles_count,
            'updated_at' => $record->updated_at,
        ]);

        return $paginator;
    }

    public function find(int $id): ?array
    {
        $record = AcquisitionContextRecord::query()->with(['tools', 'articles'])->find($id);

        if ($record === null) {
            return null;
        }

        return [
            'id' => (int) $record->getKey(),
            'name' => $record->name,
            'keyword' => $record->keyword,
            'campaign_identifier' => $record->campaign_identifier,
            'campaign_source' => $record->campaign_source,
            'campaign_medium' => $record->campaign_medium,
            'content_identifier' => $record->content_identifier,
            'video_identifier' => $record->video_identifier,
            'banner_identifier' => $record->banner_identifier,
            'cta_identifier' => $record->cta_identifier,
            'monthly_investment' => $this->moneyInput($record->monthly_investment_cents),
            'investment_currency' => $record->investment_currency ?: 'BRL',
            'status' => $record->status->value,
            'hero_title_before' => $record->hero_title_before,
            'hero_title_line' => $record->hero_title_line,
            'hero_title_highlight' => $record->hero_title_highlight,
            'hero_description' => $record->hero_description,
            'hero_search_placeholder' => $record->hero_search_placeholder,
            'tools_section_title' => $record->tools_section_title,
            'cta_title' => $record->cta_title,
            'cta_description' => $record->cta_description,
            'cta_label' => $record->cta_label,
            'cta_url' => $record->cta_url,
            'cta_tool_slug' => $record->cta_tool_slug,
            'contextual_message' => $record->contextual_message,
            'contextual_continue_label' => $record->contextual_continue_label,
            'contextual_continue_url' => $record->contextual_continue_url,
            'contextual_continue_tool_slug' => $record->contextual_continue_tool_slug,
            'primary_tool_slug' => $record->primary_tool_slug,
            'featured_tools' => $this->orderedTools($record, AcquisitionContextToolPlacement::Featured),
            'recommended_tools' => $this->orderedTools($record, AcquisitionContextToolPlacement::Recommended),
            'articles' => $record->articles->pluck('article_slug')->values()->all(),
        ];
    }

    public function save(?int $id, array $data): int
    {
        $previousKeyword = $id === null
            ? null
            : AcquisitionContextRecord::query()->whereKey($id)->value('keyword');

        $savedId = DB::transaction(function () use ($id, $data): int {
            $record = $id === null
                ? new AcquisitionContextRecord
                : AcquisitionContextRecord::query()->findOrFail($id);

            $record->fill([
                'name' => trim($data['name']),
                'keyword' => trim($data['keyword']),
                'campaign_identifier' => $this->nullable($data['campaign_identifier'] ?? null),
                'campaign_source' => $this->nullable($data['campaign_source'] ?? null),
                'campaign_medium' => $this->nullable($data['campaign_medium'] ?? null),
                'content_identifier' => $this->nullable($data['content_identifier'] ?? null),
                'video_identifier' => $this->nullable($data['video_identifier'] ?? null),
                'banner_identifier' => $this->nullable($data['banner_identifier'] ?? null),
                'cta_identifier' => $this->nullable($data['cta_identifier'] ?? null),
                'monthly_investment_cents' => $this->moneyToCents($data['monthly_investment'] ?? null),
                'investment_currency' => strtoupper((string) ($data['investment_currency'] ?? 'BRL')),
                'status' => $data['status'],
                'hero_title_before' => $this->nullable($data['hero_title_before'] ?? null),
                'hero_title_line' => $this->nullable($data['hero_title_line'] ?? null),
                'hero_title_highlight' => $this->nullable($data['hero_title_highlight'] ?? null),
                'hero_description' => $this->nullable($data['hero_description'] ?? null),
                'hero_search_placeholder' => $this->nullable($data['hero_search_placeholder'] ?? null),
                'tools_section_title' => $this->nullable($data['tools_section_title'] ?? null),
                'cta_title' => $this->nullable($data['cta_title'] ?? null),
                'cta_description' => $this->nullable($data['cta_description'] ?? null),
                'cta_label' => $this->nullable($data['cta_label'] ?? null),
                'cta_url' => $this->nullable($data['cta_url'] ?? null),
                'cta_tool_slug' => $this->nullable($data['cta_tool_slug'] ?? null),
                'contextual_message' => $this->nullable($data['contextual_message'] ?? null),
                'contextual_continue_label' => $this->nullable($data['contextual_continue_label'] ?? null),
                'contextual_continue_url' => $this->nullable($data['contextual_continue_url'] ?? null),
                'contextual_continue_tool_slug' => $this->nullable($data['contextual_continue_tool_slug'] ?? null),
                'primary_tool_slug' => $this->nullable($data['primary_tool_slug'] ?? null),
            ])->save();

            $record->tools()->delete();
            $record->articles()->delete();

            $this->insertTools($record, $data['featured_tools'] ?? [], AcquisitionContextToolPlacement::Featured);
            $this->insertTools($record, $data['recommended_tools'] ?? [], AcquisitionContextToolPlacement::Recommended);

            foreach ($this->uniqueStrings($data['articles'] ?? []) as $position => $slug) {
                $record->articles()->create(['article_slug' => $slug, 'position' => $position]);
            }

            return (int) $record->getKey();
        });

        $this->cache->forget((string) $previousKeyword);
        $this->cache->forget((string) $data['keyword']);

        return $savedId;
    }

    public function duplicate(int $id): int
    {
        $source = AcquisitionContextRecord::query()->with(['tools', 'articles'])->findOrFail($id);

        return DB::transaction(function () use ($source): int {
            $copy = $source->replicate();
            $copy->name = $source->name.' (cópia)';
            $copy->keyword = $this->availableCopyKeyword($source->keyword);
            $copy->status = AcquisitionContextStatus::Inactive;
            $copy->save();

            foreach ($source->tools as $tool) {
                $copy->tools()->create([
                    'tool_slug' => $tool->tool_slug,
                    'placement' => $tool->placement,
                    'position' => $tool->position,
                ]);
            }

            foreach ($source->articles as $article) {
                $copy->articles()->create([
                    'article_slug' => $article->article_slug,
                    'position' => $article->position,
                ]);
            }

            return (int) $copy->getKey();
        });
    }

    public function toggle(int $id): bool
    {
        $record = AcquisitionContextRecord::query()->findOrFail($id);
        $active = $record->status !== AcquisitionContextStatus::Active;
        $record->status = $active ? AcquisitionContextStatus::Active : AcquisitionContextStatus::Inactive;
        $record->save();
        $this->cache->forget($record->keyword);

        return $active;
    }

    public function delete(int $id): void
    {
        $record = AcquisitionContextRecord::query()->findOrFail($id);
        $keyword = $record->keyword;
        $record->delete();
        $this->cache->forget($keyword);
    }

    public function toolOptions(): array
    {
        return $this->tools->all(false)
            ->map(static fn (array $tool): array => ['slug' => $tool['slug'], 'name' => $tool['name']])
            ->sortBy('name')
            ->values()
            ->all();
    }

    public function articleOptions(): array
    {
        return BlogPost::query()
            ->orderBy('title')
            ->get(['slug', 'title'])
            ->map(static fn (BlogPost $post): array => ['slug' => $post->slug, 'title' => $post->title])
            ->all();
    }

    /** @return list<string> */
    private function orderedTools(AcquisitionContextRecord $record, AcquisitionContextToolPlacement $placement): array
    {
        return $record->tools
            ->filter(static fn (AcquisitionContextToolRecord $tool): bool => $tool->placement === $placement)
            ->pluck('tool_slug')
            ->values()
            ->all();
    }

    /** @param mixed $values */
    private function insertTools(AcquisitionContextRecord $record, mixed $values, AcquisitionContextToolPlacement $placement): void
    {
        foreach ($this->uniqueStrings($values) as $position => $slug) {
            $record->tools()->create([
                'tool_slug' => $slug,
                'placement' => $placement,
                'position' => $position,
            ]);
        }
    }

    /** @param mixed $values @return list<string> */
    private function uniqueStrings(mixed $values): array
    {
        if (! is_array($values)) {
            return [];
        }

        return collect($values)
            ->filter(static fn ($value): bool => is_string($value) && trim($value) !== '')
            ->map(static fn (string $value): string => trim($value))
            ->unique()
            ->values()
            ->all();
    }

    private function moneyToCents(mixed $value): ?int
    {
        $normalized = trim((string) $value);
        if ($normalized === '') {
            return null;
        }

        if (str_contains($normalized, ',') && str_contains($normalized, '.')) {
            $normalized = str_replace('.', '', $normalized);
            $normalized = str_replace(',', '.', $normalized);
        } else {
            $normalized = str_replace(',', '.', $normalized);
        }

        return max(0, (int) round(((float) $normalized) * 100));
    }

    private function moneyInput(mixed $cents): string
    {
        return $cents === null ? '' : number_format(((int) $cents) / 100, 2, ',', '.');
    }

    private function availableCopyKeyword(string $keyword): string
    {
        $base = mb_substr($keyword.'-copy', 0, 240);
        $candidate = $base;
        $suffix = 2;

        while (AcquisitionContextRecord::query()->where('keyword', $candidate)->exists()) {
            $candidate = mb_substr($base, 0, 240 - strlen((string) $suffix)).'-'.$suffix;
            $suffix++;
        }

        return $candidate;
    }

    private function nullable(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
