<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class ExamLinkButtonTest extends TestCase
{
    public function test_it_renders_a_submit_button_when_requested(): void
    {
        $html = Blade::render('<x-exam.link-button type="submit">Verify</x-exam.link-button>');

        $this->assertStringContainsString('<button type="submit"', $html);
        $this->assertStringNotContainsString('type="button"', $html);
    }
}
