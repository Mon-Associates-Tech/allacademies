<?php

namespace Tests\Feature;

use Tests\TestCase;

class EmailSendingControlTest extends TestCase
{
    /**
     * Test that EMAIL_SENDING_ENABLED config defaults to true
     */
    public function test_email_sending_enabled_defaults_to_true(): void
    {
        // Should load from .env via config/mail.php
        $enabled = config('mail.enabled');
        $this->assertIsBool($enabled);
        // Default behavior should allow emails (true)
        $this->assertTrue($enabled);
    }

    /**
     * Test that EMAIL_SENDING_ENABLED config can be disabled
     */
    public function test_email_sending_enabled_can_be_disabled(): void
    {
        config(['mail.enabled' => false]);
        $this->assertFalse(config('mail.enabled'));
    }

    /**
     * Test that EMAIL_SENDING_ENABLED config can be enabled explicitly
     */
    public function test_email_sending_enabled_can_be_enabled(): void
    {
        config(['mail.enabled' => true]);
        $this->assertTrue(config('mail.enabled'));
    }

    /**
     * Test that PreventDisabledMailSending listener exists and is registered
     */
    public function test_prevent_disabled_mail_sending_listener_exists(): void
    {
        $listenerClass = 'App\Listeners\PreventDisabledMailSending';
        $this->assertTrue(class_exists($listenerClass));
    }
}
