<?php

namespace App\Notifications;

use App\Models\Book;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookAudioConversionFailed extends Notification implements ShouldQueue
{
    use Queueable;

    public Book $book;
    public string $errorMessage;

    public function __construct(Book $book, string $errorMessage = '')
    {
        $this->book = $book;
        $this->errorMessage = $errorMessage;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Audio Conversion Failed - ' . $this->book->title)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Unfortunately, the audio conversion for your book encountered an error.')
            ->line('**Book Title:** ' . $this->book->title)
            ->line('Our team has been notified and will look into this issue.')
            ->action('View Book', route('books.show', $this->book->slug))
            ->line('We apologize for the inconvenience.');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'book_audio_conversion_failed',
            'book_id' => $this->book->id,
            'book_title' => $this->book->title,
            'book_slug' => $this->book->slug,
            'message' => "Audio conversion failed for '{$this->book->title}'. Our team has been notified.",
            'error' => $this->errorMessage,
            'icon' => 'exclamation-circle',
            'color' => 'danger',
        ];
    }
}
