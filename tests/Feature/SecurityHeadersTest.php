<?php

namespace Tests\Feature;

use Tests\TestCase;

final class SecurityHeadersTest extends TestCase
{
    public function test_web_responses_receive_platform_security_headers(): void
    {
        config()->set('operations.content_security_policy.enabled', false);

        $response = $this->get('/');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy');
        $response->assertHeaderMissing('Content-Security-Policy');
    }

    public function test_content_security_policy_is_applied_when_enabled(): void
    {
        config()->set('operations.content_security_policy.enabled', true);
        config()->set('operations.content_security_policy.value', "default-src 'self'");

        $this->get('/')
            ->assertHeader('Content-Security-Policy', "default-src 'self'");
    }
}
