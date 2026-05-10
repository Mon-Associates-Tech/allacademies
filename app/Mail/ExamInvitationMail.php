<?php

namespace App\Mail;

use App\Models\GeneralExam;
use App\Services\IcsCalendarService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExamInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public GeneralExam $exam,
        public string $participantName,
        public string $participantEmail,
        public ?string $uniqueCode = null,
        public bool $isReminder = false
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->isReminder 
            ? "Reminder: {$this->exam->title}" 
            : "Invitation: {$this->exam->title}";

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.exam-invitation',
            with: [
                'exam' => $this->exam,
                'participantName' => $this->participantName,
                'uniqueCode' => $this->uniqueCode,
                'isReminder' => $this->isReminder,
                'joinUrl' => route('examinations-hub.take.join'),
            ],
        );
    }

    public function attachments(): array
    {
        if (!$this->exam->start_datetime) {
            return [];
        }

        $icsService = app(IcsCalendarService::class);
        $icsContent = $icsService->generateIcs($this->exam);

        return [
            Attachment::fromData(fn () => $icsContent, 'exam-' . $this->exam->id . '.ics')
                ->withMime('text/calendar'),
        ];
    }
}
