<?php

namespace App\Livewire\Students\Messages;

use App\Models\Message;
use Livewire\Component;

class MessageShow extends Component
{
    public Message $message;

    public function mount(Message $message)
    {
        // Check if user is authorized to view this message
        if ($message->sender_id !== auth()->id() &&
            !$message->recipients->contains('id', auth()->id())) {
            abort(403);
        }

        // Mark as read if recipient
        if ($message->recipients->contains('id', auth()->id())) {
            $message->markAsReadByUser(auth()->user());
        }

        $this->message = $message;
    }

    public function render()
    {
        return view('livewire.students.messages.show');
    }
}
