<?php

namespace App\Core\Feedback\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ToolSuggestion extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'name',
        'email',
        'problem',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
