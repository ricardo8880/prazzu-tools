<?php

namespace App\Core\Feedback\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

final class PageFeedback extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'path',
        'url',
        'page_title',
        'rating',
        'comment',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
