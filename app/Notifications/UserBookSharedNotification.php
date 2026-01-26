<?php

namespace App\Notifications;

use App\Models\UserBook;
use App\Models\UserBookShare;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserBookSharedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected UserBookShare $share;

    protected UserBook $userBook;

    public function __construct(UserBookShare $share)
    {
        $this->share = $share;
        $this->userBook = $share->userBook;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $data = [
            'notifiable' => $notifiable,
            'share' => $this->share,
            'userBook' => $this->userBook,
            'sharedBy' => $this->share->sharedBy,
        ];

        return (new MailMessage)
            ->subject("📚 New Book Shared: {$this->userBook->title}")
            ->view('emails.user-book-shared', $data)
            ->text('emails.user-book-shared-text', $data);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => 'book_shared',
            'book_id' => $this->userBook->id,
            'book_title' => $this->userBook->title,
            'book_cover' => $this->userBook->cover_image,
            'share_id' => $this->share->id,
            'shared_by_id' => $this->share->shared_by_user_id,
            'shared_by_name' => $this->share->sharedBy->name,
            'share_type' => $this->share->share_type,
            'share_target' => $this->share->getShareTargetName(),
            'notes' => $this->share->notes,
            'expires_at' => $this->share->expires_at?->toISOString(),
            'url' => route('user-books.show', $this->userBook),
        ];
    }
}
