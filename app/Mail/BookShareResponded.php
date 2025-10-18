<?php

namespace App\Mail;

use App\Models\UserBookShare;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookShareResponded extends Mailable
{
    use Queueable, SerializesModels;

    public UserBookShare $share;
    public string $action;

    public function __construct(UserBookShare $share, string $action)
    {
        $this->share = $share;
        $this->action = $action;
    }

    public function build()
    {
        $subject = "Book share {$this->action}: {$this->share->userBook->title}";

        return $this->subject($subject)
            ->view('emails.book-share-responded')
            ->with([
                'share' => $this->share,
                'book' => $this->share->userBook,
                'recipient' => $this->share->sharedTo,
                'action' => $this->action,
            ]);
    }
}
