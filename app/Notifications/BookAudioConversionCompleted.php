<?php

namespace App\Notifications;

use App\Models\Book;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookAudioConversionCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    public Book $book;
    public int $chaptersCount;

    public function __construct(Book $book, int $chaptersCount = 0)
    {
        $this->book = $book;
        $this->chaptersCount = $chaptersCount;
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
        return (new MailMessage)
            ->subject('Audio Conversion Completed - ' . $this->book->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Great news! The audio conversion for your book has been completed.')
            ->line('**Book Title:** ' . $this->book->title)
            ->line('**Chapters Converted:** ' . $this->chaptersCount)
            ->action('View Book', route('books.show', $this->book->slug))
            ->line('Your book is now available with audio narration!')
            ->line('Thank you for using our platform!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => 'book_audio_conversion_completed',
            'book_id' => $this->book->id,
            'book_title' => $this->book->title,
            'book_slug' => $this->book->slug,
            'chapters_count' => $this->chaptersCount,
            'message' => "Audio conversion completed for '{$this->book->title}' with {$this->chaptersCount} chapters.",
            'icon' => 'microphone',
            'color' => 'success',
        ];
    }
}
