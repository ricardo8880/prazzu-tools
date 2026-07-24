<?php

declare(strict_types=1);

namespace App\Core\Feedback\Models;

use App\Core\Feedback\Enums\ToolFeedbackStatus;
use App\Core\Feedback\Enums\ToolFeedbackType;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ToolFeedback extends Model
{
    protected $table = 'tool_feedback';

    protected $fillable = [
        'user_id',
        'session_id',
        'tool_slug',
        'tool_name',
        'tool_version',
        'type',
        'status',
        'message',
        'attempted_action',
        'path',
        'url',
        'context',
        'user_agent',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => ToolFeedbackType::class,
            'status' => ToolFeedbackStatus::class,
            'context' => 'array',
            'reviewed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
