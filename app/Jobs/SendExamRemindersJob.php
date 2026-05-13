<?php

namespace App\Jobs;

use App\Mail\ExamInvitationMail;
use App\ExaminationHub\Models\GeneralExam;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendExamRemindersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public GeneralExam $exam,
        public bool $isReminder = false
    ) {}

    public function handle(): void
    {
        $participants = $this->exam->configuredParticipants()
            ->where('is_active', true)
            ->get();

        foreach ($participants as $participant) {
            if (!$participant->email) {
                continue;
            }

            Mail::to($participant->email)->send(
                new ExamInvitationMail(
                    exam: $this->exam,
                    participantName: $participant->name,
                    participantEmail: $participant->email,
                    uniqueCode: $participant->unique_code,
                    isReminder: $this->isReminder
                )
            );
        }

        if ($this->isReminder) {
            $this->exam->update([
                'reminder_sent' => true,
                'reminder_sent_at' => now(),
            ]);
        }
    }
}
