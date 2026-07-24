<?php

declare(strict_types=1);

namespace App\Core\Feedback\Application;

use App\Core\Feedback\Data\ToolFeedbackSubmission;
use App\Core\Feedback\Enums\ToolFeedbackStatus;
use App\Core\Feedback\Models\ToolFeedback;
use App\Core\Tools\ToolRegistry;
use InvalidArgumentException;

final readonly class StoreToolFeedback
{
    public function __construct(private ToolRegistry $tools) {}

    public function execute(ToolFeedbackSubmission $submission): ToolFeedback
    {
        $manifest = $this->tools->findManifest($submission->toolSlug);

        if ($manifest === null) {
            throw new InvalidArgumentException("A ferramenta [{$submission->toolSlug}] não está registrada.");
        }

        return ToolFeedback::query()->create([
            'user_id' => $submission->userId,
            'session_id' => $submission->sessionId,
            'tool_slug' => $manifest->slug,
            'tool_name' => $manifest->name,
            'tool_version' => $manifest->version,
            'type' => $submission->type,
            'status' => ToolFeedbackStatus::New,
            'message' => $submission->normalizedMessage(),
            'attempted_action' => $submission->normalizedAttemptedAction(),
            'path' => $submission->path,
            'url' => $submission->url,
            'context' => $submission->context === [] ? null : $submission->context,
            'user_agent' => $submission->userAgent,
        ]);
    }
}
