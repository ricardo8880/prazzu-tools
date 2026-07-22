<?php

namespace App\Core\Analytics\Domain\ValueObjects;

use App\Core\Acquisition\Domain\Data\AcquisitionAnalyticsSnapshot;

final readonly class AnalyticsContext
{
    /** @param array<string, string|null> $utm */
    public function __construct(
        public ?string $visitorId = null,
        public ?string $analyticsSessionId = null,
        public int|string|null $userId = null,
        public ?string $laravelSessionId = null,
        public ?string $url = null,
        public ?string $path = null,
        public ?string $referrer = null,
        public ?string $source = null,
        public ?string $medium = null,
        public ?string $campaign = null,
        public ?AcquisitionAnalyticsSnapshot $acquisition = null,
        public array $utm = [],
        public ?string $deviceType = null,
        public ?string $browser = null,
        public ?string $operatingSystem = null,
        public ?string $language = null,
        public ?string $timezone = null,
        public ?string $screenResolution = null,
        public ?string $countryCode = null,
        public ?string $region = null,
        public ?string $city = null,
        public ?string $ipHash = null,
        public ?string $userAgent = null,
    ) {}
}
