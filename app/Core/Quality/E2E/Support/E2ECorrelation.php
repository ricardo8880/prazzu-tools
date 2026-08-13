<?php

declare(strict_types=1);

namespace App\Core\Quality\E2E\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

final class E2ECorrelation
{
    public const RUN_HEADER = 'X-E2E-Run-Id';

    public const SCENARIO_HEADER = 'X-E2E-Scenario-Id';

    public static function fromRequest(Request $request): array
    {
        return [
            'e2e_run_id' => self::normalize($request->header(self::RUN_HEADER), 'run'),
            'e2e_scenario_id' => self::normalize($request->header(self::SCENARIO_HEADER), 'scenario'),
        ];
    }

    public static function normalize(?string $value, string $prefix): string
    {
        $value = strtolower(trim((string) $value));
        $value = preg_replace('/[^a-z0-9._:-]+/', '-', $value) ?? '';
        $value = trim($value, '-.');

        return $value !== '' ? substr($value, 0, 120) : $prefix.'-'.Str::uuid()->toString();
    }
}
