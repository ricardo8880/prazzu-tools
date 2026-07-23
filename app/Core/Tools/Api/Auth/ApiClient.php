<?php

namespace App\Core\Tools\Api\Auth;

final readonly class ApiClient
{
    /** @param list<string> $abilities */
    public function __construct(
        public string $id,
        public string $name,
        public array $abilities,
    ) {}

    public function can(string $ability): bool
    {
        if (in_array('*', $this->abilities, true) || in_array($ability, $this->abilities, true)) {
            return true;
        }

        foreach ($this->abilities as $grantedAbility) {
            if (! str_ends_with($grantedAbility, ':*')) {
                continue;
            }

            if (str_starts_with($ability, substr($grantedAbility, 0, -1))) {
                return true;
            }
        }

        return false;
    }
}
