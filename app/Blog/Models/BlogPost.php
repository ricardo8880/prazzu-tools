<?php

namespace App\Blog\Models;

use App\Blog\Enums\BlogPostStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class BlogPost extends Model
{
    protected $fillable = [
        'author_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'category_id',
        'category',
        'cover_image_path',
        'cover_image_alt',
        'status',
        'is_featured',
        'published_at',
        'content_updated_at',
        'primary_keyword',
        'related_keywords',
        'meta_title',
        'meta_description',
        'canonical_url',
        'social_image_path',
        'should_index',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function blogCategory(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    public function scopePubliclyAvailable(Builder $query, ?\DateTimeInterface $now = null): Builder
    {
        $reference = $now ?? now();

        return $query
            ->where('status', BlogPostStatus::Published->value)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', $reference)
            ->orderByDesc('published_at');
    }

    public function isPubliclyAvailable(?\DateTimeInterface $now = null): bool
    {
        return $this->status->canBePublicAt(
            $this->published_at,
            $now ?? now(),
        );
    }

    public function readingTimeMinutes(int $wordsPerMinute = 200): int
    {
        $wordCount = str_word_count(strip_tags($this->content));

        return max(1, (int) ceil($wordCount / max(1, $wordsPerMinute)));
    }

    /** @return Collection<int, string> */
    public function relatedToolSlugs(): Collection
    {
        if (! $this->exists) {
            return collect();
        }

        return DB::table('blog_post_tool')
            ->where('blog_post_id', $this->getKey())
            ->orderBy('tool_slug')
            ->pluck('tool_slug');
    }

    /** @param iterable<int, string> $toolSlugs */
    public function syncRelatedToolSlugs(iterable $toolSlugs): void
    {
        if (! $this->exists) {
            throw new \LogicException('A postagem precisa estar salva antes de relacionar ferramentas.');
        }

        $normalized = collect($toolSlugs)
            ->map(static fn (string $slug): string => trim($slug))
            ->filter()
            ->unique()
            ->values();

        DB::transaction(function () use ($normalized): void {
            DB::table('blog_post_tool')
                ->where('blog_post_id', $this->getKey())
                ->delete();

            if ($normalized->isEmpty()) {
                return;
            }

            DB::table('blog_post_tool')->insert(
                $normalized->map(fn (string $slug): array => [
                    'blog_post_id' => $this->getKey(),
                    'tool_slug' => $slug,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->all(),
            );
        });
    }

    protected function casts(): array
    {
        return [
            'status' => BlogPostStatus::class,
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
            'content_updated_at' => 'datetime',
            'related_keywords' => 'array',
            'should_index' => 'boolean',
        ];
    }
}
