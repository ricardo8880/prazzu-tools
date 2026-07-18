<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Application\Access;

use App\Tools\AccountingFeesCalculator\Application\Data\AccountingFeesOwner;

final class AccountingFeesRecordAccess
{
    public function ensureOwnedBy(
        AccountingFeesOwner $owner,
        int|string|null $recordUserId,
        ?string $recordSessionKey,
    ): void {
        $allowed = $owner->userId !== null
            ? (int) $recordUserId === $owner->userId
            : $recordUserId === null
                && hash_equals((string) $recordSessionKey, $owner->sessionKey);

        abort_unless($allowed, 404);
    }
}
