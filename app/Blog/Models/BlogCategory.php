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
