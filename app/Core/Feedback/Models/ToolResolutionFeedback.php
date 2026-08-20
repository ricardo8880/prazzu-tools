<?php

declare(strict_types=1);

namespace App\Core\Feedback\Models;

use App\Core\Feedback\Enums\ToolResolution;
use App\Core\Feedback\Enums\ToolResolutionReason;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ToolResolutionFeedback extends Model
{
    protected $table = 'tool_resolution_feedback';

    protected $fillable = [
        'user_id', 'session_id', 'tool_slug', 'tool_name', 'tool_version',
        'resolution', 'reason', 'comment', 'path', 'url', 'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'resolution' => ToolResolution::class,
            'reason' => ToolResolutionReason::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
