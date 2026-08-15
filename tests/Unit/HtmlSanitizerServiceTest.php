<?php

namespace Tests\Unit;

use App\Services\HtmlSanitizerService;
use Tests\TestCase;

class HtmlSanitizerServiceTest extends TestCase
{
    public function test_it_removes_executable_markup_but_preserves_safe_formatting(): void
    {
        $clean = HtmlSanitizerService::sanitize(
            '<p onclick="alert(1)"><strong>Safe</strong></p>'
            .'<script>alert(2)</script>'
            .'<img src="javascript:alert(3)" onerror="alert(4)">'
            .'<iframe src="https://evil.example/embed/abcdefghijk"></iframe>'
        );

        $this->assertStringContainsString('<p><strong>Safe</strong></p>', $clean);
        $this->assertStringNotContainsString('onclick', $clean);
        $this->assertStringNotContainsString('<script', $clean);
        $this->assertStringNotContainsString('javascript:', $clean);
        $this->assertStringNotContainsString('<iframe', $clean);
    }
}
