<?php

namespace App\Console\Commands;

use App\Jobs\SendExamRemindersJob;
use App\ExaminationHub\Models\GeneralExam;
use Illuminate\Console\Command;

class SendScheduledExamReminders extends Command
{
    protected $signature = 'exams:send-reminders';
    protected $description = 'Send scheduled exam reminders';

    public function handle(): int
    {
        $exams = GeneralExam::where('send_reminders', true)
            ->where('reminder_sent', false)
            ->whereNotNull('reminder_datetime')
            ->where('reminder_datetime', '<=', now())
            ->get();

        if ($exams->isEmpty()) {
            $this->info('No reminders to send.');
            return self::SUCCESS;
        }

        foreach ($exams as $exam) {
            $this->info("Sending reminders for: {$exam->title}");
            SendExamRemindersJob::dispatch($exam, true);
        }

        $this->info("Queued reminders for {$exams->count()} exam(s).");
        return self::SUCCESS;
    }
}
