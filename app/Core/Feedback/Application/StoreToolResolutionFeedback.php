<?php

declare(strict_types=1);

namespace App\Core\Feedback\Application;

use App\Core\Feedback\Data\ToolResolutionSubmission;
use App\Core\Feedback\Models\ToolResolutionFeedback;
use App\Core\Tools\ToolRegistry;
use InvalidArgumentException;

final readonly class StoreToolResolutionFeedback
{
    public function __construct(private ToolRegistry $tools) {}

    public function execute(ToolResolutionSubmission $submission): ToolResolutionFeedback
    {
        $manifest = $this->tools->findManifest($submission->toolSlug);

        if ($manifest === null) {
            throw new InvalidArgumentException("A ferramenta [{$submission->toolSlug}] não está registrada.");
        }

        return ToolResolutionFeedback::query()->create([
            'user_id' => $submission->userId,
            'session_id' => $submission->sessionId,
            'tool_slug' => $manifest->slug,
            'tool_name' => $manifest->name,
            'tool_version' => $manifest->version,
            'resolution' => $submission->resolution,
            'reason' => $submission->reason,
            'comment' => $submission->normalizedComment(),
            'path' => $submission->path,
            'url' => $submission->url,
            'user_agent' => $submission->userAgent,
        ]);
    }
}
