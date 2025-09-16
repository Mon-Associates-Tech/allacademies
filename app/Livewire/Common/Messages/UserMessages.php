<?php

namespace App\Livewire\Common\Messages;

use App\Models\MessageRecipient;
use Livewire\Component;
use Livewire\WithPagination;

class UserMessages extends Component
{
    use WithPagination;

    public $search = '';
    public $readFilter = 'all';
    public $selectedMessage = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'readFilter' => ['except' => 'all'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingReadFilter()
    {
        $this->resetPage();
    }

    public function markAsRead($messageId)
    {
        $recipient = MessageRecipient::where('message_id', $messageId)
            ->where('user_id', auth()->id())
            ->first();

        if ($recipient) {
            $recipient->markAsRead();
        }
    }

    public function markAllAsRead()
    {
        MessageRecipient::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        session()->flash('success', 'All messages marked as read.');
    }

    public function openMessage($messageId)
    {
        $this->selectedMessage = $messageId;
        $this->markAsRead($messageId);
    }

    public function closeMessage()
    {
        $this->selectedMessage = null;
    }

    public function getMessagesProperty()
    {
        $query = MessageRecipient::where('user_id', auth()->id())
            ->with(['message.sender', 'message.attachments'])
            ->when($this->search, function ($query) {
                $query->whereHas('message', function ($q) {
                    $q->where('subject', 'like', '%' . $this->search . '%')
                        ->orWhere('body', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->readFilter === 'unread', function ($query) {
                $query->whereNull('read_at');
            })
            ->when($this->readFilter === 'read', function ($query) {
                $query->whereNotNull('read_at');
            })
            ->orderBy('created_at', 'desc');

        return $query->paginate(15);
    }

    public function getUnreadCountProperty()
    {
        return MessageRecipient::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->count();
    }

    public function render()
    {
        return view('livewire.common.user-messages', [
            'messages' => $this->messages,
            'unreadCount' => $this->unreadCount,
        ]);
    }
}
