<?php

namespace App\Core\Acquisition\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class AcquisitionContextArticleRecord extends Model
{
    protected $table = 'acquisition_context_articles';

    protected $fillable = [
        'acquisition_context_id',
        'article_slug',
        'position',
    ];

    public function context(): BelongsTo
    {
        return $this->belongsTo(AcquisitionContextRecord::class, 'acquisition_context_id');
    }

    protected function casts(): array
    {
        return [
            'position' => 'integer',
        ];
    }
}
