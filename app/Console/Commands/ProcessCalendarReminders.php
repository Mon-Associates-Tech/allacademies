<?php

namespace App\Console\Commands;

use App\Services\CalendarReminderService;
use Illuminate\Console\Command;

class ProcessCalendarReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calendar:process-reminders
                            {--dry-run : Show what would be processed without actually sending}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process and send due calendar event reminders';

    /**
     * Execute the console command.
     */
    public function handle(CalendarReminderService $reminderService): int
    {
        $this->info('Processing calendar reminders...');

        if ($this->option('dry-run')) {
            return $this->dryRun($reminderService);
        }

        $stats = $reminderService->processDueReminders();

        $this->info("Processed: {$stats['processed']}");
        $this->info("Sent: {$stats['sent']}");

        if ($stats['failed'] > 0) {
            $this->warn("Failed: {$stats['failed']}");
        }

        if ($stats['processed'] === 0) {
            $this->info('No reminders to process.');
        }

        return self::SUCCESS;
    }

    /**
     * Show what would be processed without sending.
     */
    protected function dryRun(CalendarReminderService $reminderService): int
    {
        $dueReminders = \App\Models\CalendarEventReminder::query()
            ->due()
            ->with(['calendarEvent', 'user'])
            ->get();

        if ($dueReminders->isEmpty()) {
            $this->info('No reminders are currently due.');

            return self::SUCCESS;
        }

        $this->info("Found {$dueReminders->count()} due reminder(s):");
        $this->newLine();

        $headers = ['ID', 'Event', 'User', 'Remind At', 'Channels', 'Minutes Before'];
        $rows = $dueReminders->map(function ($reminder) {
            return [
                $reminder->id,
                $reminder->calendarEvent?->title ?? 'N/A',
                $reminder->user?->name ?? 'N/A',
                $reminder->remind_at->format('Y-m-d H:i:s'),
                implode(', ', $reminder->channels ?? []),
                $reminder->minutes_before,
            ];
        })->toArray();

        $this->table($headers, $rows);

        return self::SUCCESS;
    }
}
