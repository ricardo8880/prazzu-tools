<?php

namespace App\Blog\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class BlogCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'vertical_slug',
        'is_active',
    ];

    protected static function booted(): void
    {
        self::creating(static function (BlogCategory $category): void {
            if (! is_string($category->vertical_slug) || trim($category->vertical_slug) === '') {
                $defaultVertical = config('verticals.default');
                $category->vertical_slug = is_string($defaultVertical) && trim($defaultVertical) !== ''
                    ? trim($defaultVertical)
                    : null;
            }
        });
    }

    public function scopeForVertical(Builder $query, ?string $vertical): Builder
    {
        return $vertical === null ? $query : $query->where('vertical_slug', $vertical);
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function posts(): HasMany
    {
        return $this->hasMany(BlogPost::class, 'category_id');
    }
}
