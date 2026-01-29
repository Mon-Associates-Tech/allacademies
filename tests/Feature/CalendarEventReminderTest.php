<?php

namespace Tests\Feature;

use App\Enums\NotificationChannel;
use App\Models\CalendarEvent;
use App\Models\CalendarEventReminder;
use App\Models\User;
use App\Notifications\CalendarEventReminderNotification;
use App\Services\CalendarReminderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CalendarEventReminderTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected CalendarEvent $event;

    protected CalendarReminderService $reminderService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->reminderService = new CalendarReminderService;

        // Create a calendar event starting in 2 hours
        $this->event = CalendarEvent::create([
            'title' => 'Test Event',
            'description' => 'Test Description',
            'start_date' => Carbon::now()->addHours(2),
            'end_date' => Carbon::now()->addHours(3),
            'all_day' => false,
            'user_id' => $this->user->id,
            'event_type' => CalendarEvent::class,
            'event_id' => 0,
            'visibility' => 'private',
        ]);
    }

    /** @test */
    public function it_can_create_a_reminder_for_an_event()
    {
        $reminder = CalendarEventReminder::createForEvent(
            $this->event,
            $this->user->id,
            15,
            ['email', 'database']
        );

        $this->assertDatabaseHas('calendar_event_reminders', [
            'id' => $reminder->id,
            'calendar_event_id' => $this->event->id,
            'user_id' => $this->user->id,
            'minutes_before' => 15,
            'status' => CalendarEventReminder::STATUS_PENDING,
            'is_sent' => false,
        ]);

        $this->assertEquals(['email', 'database'], $reminder->channels);
    }

    /** @test */
    public function it_calculates_remind_at_correctly()
    {
        $reminder = CalendarEventReminder::createForEvent(
            $this->event,
            $this->user->id,
            30
        );

        $expectedRemindAt = $this->event->start_date->copy()->subMinutes(30);

        $this->assertEquals(
            $expectedRemindAt->format('Y-m-d H:i'),
            $reminder->remind_at->format('Y-m-d H:i')
        );
    }

    /** @test */
    public function it_can_create_multiple_reminders_via_service()
    {
        $reminders = $this->reminderService->createReminders(
            $this->event,
            $this->user,
            [15, 60], // 15 min and 1 hour before
            ['email']
        );

        $this->assertCount(2, $reminders);
        $this->assertDatabaseCount('calendar_event_reminders', 2);
    }

    /** @test */
    public function it_does_not_create_reminder_if_remind_time_is_in_past()
    {
        // Create an event starting in 10 minutes
        $soonEvent = CalendarEvent::create([
            'title' => 'Soon Event',
            'start_date' => Carbon::now()->addMinutes(10),
            'user_id' => $this->user->id,
            'event_type' => CalendarEvent::class,
            'event_id' => 0,
            'visibility' => 'private',
        ]);

        // Try to create a 30-minute reminder (would be in the past)
        $reminders = $this->reminderService->createReminders(
            $soonEvent,
            $this->user,
            [30], // 30 min before would be 20 min ago
            ['email']
        );

        $this->assertCount(0, $reminders);
    }

    /** @test */
    public function it_can_mark_reminder_as_sent()
    {
        $reminder = CalendarEventReminder::createForEvent(
            $this->event,
            $this->user->id
        );

        $this->assertTrue($reminder->isPending());

        $reminder->markAsSent();

        $this->assertFalse($reminder->isPending());
        $this->assertEquals(CalendarEventReminder::STATUS_SENT, $reminder->status);
        $this->assertTrue($reminder->is_sent);
        $this->assertNotNull($reminder->sent_at);
    }

    /** @test */
    public function it_can_mark_reminder_as_failed()
    {
        $reminder = CalendarEventReminder::createForEvent(
            $this->event,
            $this->user->id
        );

        $reminder->markAsFailed('Test failure reason');

        $this->assertEquals(CalendarEventReminder::STATUS_FAILED, $reminder->status);
        $this->assertEquals('Test failure reason', $reminder->failure_reason);
    }

    /** @test */
    public function it_can_cancel_reminder()
    {
        $reminder = CalendarEventReminder::createForEvent(
            $this->event,
            $this->user->id
        );

        $reminder->cancel();

        $this->assertEquals(CalendarEventReminder::STATUS_CANCELLED, $reminder->status);
        $this->assertFalse($reminder->isPending());
    }

    /** @test */
    public function it_can_query_due_reminders()
    {
        // Create a reminder that is due (remind_at in the past)
        $dueReminder = CalendarEventReminder::create([
            'calendar_event_id' => $this->event->id,
            'user_id' => $this->user->id,
            'remind_at' => Carbon::now()->subMinutes(5),
            'minutes_before' => 15,
            'channels' => ['email'],
            'status' => CalendarEventReminder::STATUS_PENDING,
            'is_sent' => false,
        ]);

        // Create a reminder that is not yet due
        $futureReminder = CalendarEventReminder::create([
            'calendar_event_id' => $this->event->id,
            'user_id' => $this->user->id,
            'remind_at' => Carbon::now()->addMinutes(30),
            'minutes_before' => 60,
            'channels' => ['email'],
            'status' => CalendarEventReminder::STATUS_PENDING,
            'is_sent' => false,
        ]);

        $dueReminders = CalendarEventReminder::due()->get();

        $this->assertCount(1, $dueReminders);
        $this->assertEquals($dueReminder->id, $dueReminders->first()->id);
    }

    /** @test */
    public function it_sends_notification_when_processing_due_reminders()
    {
        Notification::fake();

        // Create a due reminder
        CalendarEventReminder::create([
            'calendar_event_id' => $this->event->id,
            'user_id' => $this->user->id,
            'remind_at' => Carbon::now()->subMinutes(1),
            'minutes_before' => 15,
            'channels' => ['email', 'database'],
            'status' => CalendarEventReminder::STATUS_PENDING,
            'is_sent' => false,
        ]);

        $stats = $this->reminderService->processDueReminders();

        $this->assertEquals(1, $stats['processed']);
        $this->assertEquals(1, $stats['sent']);
        $this->assertEquals(0, $stats['failed']);

        Notification::assertSentTo(
            $this->user,
            CalendarEventReminderNotification::class
        );
    }

    /** @test */
    public function it_can_cancel_all_reminders_for_event()
    {
        $this->reminderService->createReminders(
            $this->event,
            $this->user,
            [15, 60]
        );

        $this->assertDatabaseCount('calendar_event_reminders', 2);

        $cancelled = $this->event->cancelAllReminders();

        $this->assertEquals(2, $cancelled);

        $pendingCount = CalendarEventReminder::pending()->count();
        $this->assertEquals(0, $pendingCount);
    }

    /** @test */
    public function notification_channel_enum_works_correctly()
    {
        $this->assertEquals('email', NotificationChannel::EMAIL->value);
        $this->assertEquals('database', NotificationChannel::DATABASE->value);
        $this->assertEquals('sms', NotificationChannel::SMS->value);

        $this->assertEquals('Email', NotificationChannel::EMAIL->label());
        $this->assertEquals('In-App Notification', NotificationChannel::DATABASE->label());
        $this->assertEquals('SMS', NotificationChannel::SMS->label());

        // Email and database should be available
        $this->assertTrue(NotificationChannel::EMAIL->isAvailable());
        $this->assertTrue(NotificationChannel::DATABASE->isAvailable());
    }

    /** @test */
    public function reminder_has_channel_check_works()
    {
        $reminder = CalendarEventReminder::createForEvent(
            $this->event,
            $this->user->id,
            15,
            ['email', 'database']
        );

        $this->assertTrue($reminder->hasChannel('email'));
        $this->assertTrue($reminder->hasChannel('database'));
        $this->assertFalse($reminder->hasChannel('sms'));

        // Also works with enum
        $this->assertTrue($reminder->hasChannel(NotificationChannel::EMAIL));
        $this->assertFalse($reminder->hasChannel(NotificationChannel::SMS));
    }

    /** @test */
    public function calendar_event_can_create_reminders_directly()
    {
        $reminder = $this->event->createReminder(
            $this->user->id,
            30,
            ['email']
        );

        $this->assertInstanceOf(CalendarEventReminder::class, $reminder);
        $this->assertEquals($this->event->id, $reminder->calendar_event_id);
        $this->assertEquals(30, $reminder->minutes_before);
    }

    /** @test */
    public function it_can_reschedule_reminders_when_event_time_changes()
    {
        $reminder = CalendarEventReminder::createForEvent(
            $this->event,
            $this->user->id,
            15
        );

        $originalRemindAt = $reminder->remind_at->copy();

        // Update event start time
        $this->event->update([
            'start_date' => $this->event->start_date->addHour(),
        ]);

        $this->reminderService->rescheduleReminder($reminder, $this->event);

        $reminder->refresh();

        $this->assertNotEquals(
            $originalRemindAt->format('Y-m-d H:i'),
            $reminder->remind_at->format('Y-m-d H:i')
        );

        $expectedNewRemindAt = $this->event->start_date->copy()->subMinutes(15);
        $this->assertEquals(
            $expectedNewRemindAt->format('Y-m-d H:i'),
            $reminder->remind_at->format('Y-m-d H:i')
        );
    }

    /** @test */
    public function process_reminders_command_works()
    {
        Notification::fake();

        // Create a due reminder
        CalendarEventReminder::create([
            'calendar_event_id' => $this->event->id,
            'user_id' => $this->user->id,
            'remind_at' => Carbon::now()->subMinutes(1),
            'minutes_before' => 15,
            'channels' => ['database'],
            'status' => CalendarEventReminder::STATUS_PENDING,
            'is_sent' => false,
        ]);

        $this->artisan('calendar:process-reminders')
            ->expectsOutput('Processing calendar reminders...')
            ->expectsOutput('Processed: 1')
            ->expectsOutput('Sent: 1')
            ->assertSuccessful();
    }

    /** @test */
    public function process_reminders_command_dry_run_works()
    {
        // Create a due reminder
        CalendarEventReminder::create([
            'calendar_event_id' => $this->event->id,
            'user_id' => $this->user->id,
            'remind_at' => Carbon::now()->subMinutes(1),
            'minutes_before' => 15,
            'channels' => ['email'],
            'status' => CalendarEventReminder::STATUS_PENDING,
            'is_sent' => false,
        ]);

        $this->artisan('calendar:process-reminders --dry-run')
            ->expectsOutput('Found 1 due reminder(s):')
            ->assertSuccessful();

        // Reminder should still be pending after dry run
        $this->assertDatabaseHas('calendar_event_reminders', [
            'status' => CalendarEventReminder::STATUS_PENDING,
            'is_sent' => false,
        ]);
    }
}
