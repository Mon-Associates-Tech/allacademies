<?php

namespace App\Console;

use App\Jobs\ResetMonthlySubscriptionCycles;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('subscription:expired')->dailyAt('09:00');
        $schedule->command('subscriptions:expire-cycles')->hourly();
        $schedule->command('users:update-online-status')->everyMinute();
        $schedule->command('sessions:cleanup --timeout=30')->everyFiveMinutes();
        $schedule->command('messages:send-scheduled')->everyMinute();
        $schedule->command('calendar:process-reminders')->everyMinute();
        $schedule->command('tokens:check-expired')->daily();

        // Auto-submit exam submissions that have exceeded their time limit
        $schedule->command('examination-hub:auto-submit-expired')
            ->everyMinute()
            ->withoutOverlapping();

        // Generate recurring sessions daily
        $schedule->job(new \App\Jobs\GenerateRecurringSessionsJob)
            ->dailyAt('00:00')
            ->name('generate-recurring-sessions')
            ->withoutOverlapping();

        // Send session reminders 15 minutes before start
        $schedule->job(new \App\Jobs\SendSessionRemindersJob(15))
            ->everyFiveMinutes();

        // Check for ended sessions every 10 minutes
        $schedule->job(new \App\Jobs\CheckEndedSessionsJob)
            ->everyTenMinutes();

        // Cleanup expired recordings daily
        $schedule->job(new \App\Jobs\CleanupExpiredRecordingsJob)
            ->daily();

        $schedule->command('books:process-audio-conversion')->everyFiveMinutes();

        $schedule->command('queue:work --stop-when-empty --max-time=240')
            ->everyFiveMinutes()
            ->withoutOverlapping();

        $schedule->job(new ResetMonthlySubscriptionCycles)
            ->dailyAt('00:00')
            ->name('reset-monthly-subscription-cycles')
            ->withoutOverlapping();

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
