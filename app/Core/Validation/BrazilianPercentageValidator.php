<?php

declare(strict_types=1);

namespace App\Core\Validation;

use App\Core\Money\Percentage;
use Throwable;

final class BrazilianPercentageValidator
{
    public function isValid(mixed $value): bool
    {
        return $this->parse($value) !== null;
    }

    /** @param array<int, string> $parameters */
    public function hasMinimum(mixed $value, array $parameters): bool
    {
        $percentage = $this->parse($value);
        $minimum = $this->parse($parameters[0] ?? null);

        return $percentage !== null
            && $minimum !== null
            && $percentage->millionthsOfPercent() >= $minimum->millionthsOfPercent();
    }

    /** @param array<int, string> $parameters */
    public function hasMaximum(mixed $value, array $parameters): bool
    {
        $percentage = $this->parse($value);
        $maximum = $this->parse($parameters[0] ?? null);

        return $percentage !== null
            && $maximum !== null
            && $percentage->millionthsOfPercent() <= $maximum->millionthsOfPercent();
    }

    private function parse(mixed $value): ?Percentage
    {
        if ((! is_string($value) && ! is_int($value)) || trim((string) $value) === '') {
            return null;
        }

        try {
            return Percentage::fromString((string) $value);
        } catch (Throwable) {
            return null;
        }
    }
}
