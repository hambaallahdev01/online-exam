<?php

namespace Tests\Unit;

use App\Rules\TurnstileRule;
use Tests\TestCase;

class TurnstileRuleTest extends TestCase
{
    public function test_turnstile_rule_passes_when_secret_key_is_not_configured()
    {
        config(['services.turnstile.secret_key' => null]);

        $rule = new TurnstileRule();
        $failed = false;

        $rule->validate('cf-turnstile-response', 'test-token', function () use (&$failed) {
            $failed = true;
        });

        $this->assertFalse($failed);
    }
}
