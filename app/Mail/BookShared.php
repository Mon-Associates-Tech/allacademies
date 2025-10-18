<?php

namespace App\Mail;

use App\Models\UserBookShare;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookShared extends Mailable
{
    use Queueable, SerializesModels;

    public UserBookShare $share;

    public function __construct(UserBookShare $share)
    {
        $this->share = $share;
    }

    public function build()
    {
        return $this->subject("A book has been shared with you: {$this->share->userBook->title}")
            ->view('emails.book-shared')
            ->with([
                'share' => $this->share,
                'book' => $this->share->userBook,
                'sharer' => $this->share->sharedBy,
            ]);
    }
}
