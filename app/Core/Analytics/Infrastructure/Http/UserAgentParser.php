<?php

namespace App\Core\Analytics\Infrastructure\Http;

final class UserAgentParser
{
    /** @return array{device_type:string,browser:string,operating_system:string} */
    public function parse(?string $userAgent): array
    {
        $agent = strtolower($userAgent ?? '');

        $device = str_contains($agent, 'tablet') || str_contains($agent, 'ipad') ? 'tablet'
            : (str_contains($agent, 'mobile') || str_contains($agent, 'android') || str_contains($agent, 'iphone') ? 'mobile' : 'desktop');

        $browser = match (true) {
            str_contains($agent, 'edg/') => 'Edge',
            str_contains($agent, 'opr/') || str_contains($agent, 'opera') => 'Opera',
            str_contains($agent, 'chrome/') || str_contains($agent, 'crios/') => 'Chrome',
            str_contains($agent, 'firefox/') || str_contains($agent, 'fxios/') => 'Firefox',
            str_contains($agent, 'safari/') => 'Safari',
            default => 'Unknown',
        };

        $os = match (true) {
            str_contains($agent, 'windows') => 'Windows',
            str_contains($agent, 'android') => 'Android',
            str_contains($agent, 'iphone') || str_contains($agent, 'ipad') || str_contains($agent, 'ios') => 'iOS',
            str_contains($agent, 'mac os') || str_contains($agent, 'macintosh') => 'macOS',
            str_contains($agent, 'linux') => 'Linux',
            default => 'Unknown',
        };

        return ['device_type' => $device, 'browser' => $browser, 'operating_system' => $os];
    }
}
