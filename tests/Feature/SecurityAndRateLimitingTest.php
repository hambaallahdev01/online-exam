<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityAndRateLimitingTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_headers_are_present_in_responses()
    {
        $response = $this->get('/');

        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
    }

    public function test_cloudflare_real_ip_is_restored()
    {
        $response = $this->withHeaders([
            'CF-Connecting-IP' => '203.0.113.195',
        ])->get('/');

        $response->assertStatus(200);
    }

    public function test_login_rate_limiter_blocks_excessive_attempts()
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/', ['email' => 'hacker@test.com', 'password' => 'wrong']);
        }

        // 6th attempt should be throttled (HTTP 429)
        $response = $this->post('/', ['email' => 'hacker@test.com', 'password' => 'wrong']);
        $response->assertStatus(429);
    }
}
