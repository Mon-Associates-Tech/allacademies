<?php

namespace App\Livewire\Teachers\Messages;

use App\Models\Message;
use App\Services\MessageService;
use Livewire\Component;
use Livewire\WithPagination;

class MessageShow extends Component
{
    use WithPagination;

    public Message $message;

    public $activeTab = 'details';

    public $searchRecipients = '';

    public $readStatusFilter = 'all';

    protected $queryString = [
        'activeTab' => ['except' => 'details'],
        'searchRecipients' => ['except' => ''],
        'readStatusFilter' => ['except' => 'all'],
    ];

    public function mount(Message $message)
    {
        // Check if user is the sender of this message
        if ($message->sender_id !== auth()->id()) {
            abort(403, 'You do not have permission to view this message.');
        }

        $this->message = $message->load(['sender', 'attachments']);
    }

    public function updatingSearchRecipients()
    {
        $this->resetPage();
    }

    public function updatingReadStatusFilter()
    {
        $this->resetPage();
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function resendMessage()
    {
        if ($this->message->status !== Message::STATUS_FAILED) {
            session()->flash('error', 'Only failed messages can be resent.');

            return;
        }

        try {
            $messageService = app(MessageService::class);
            $messageService->sendMessage($this->message);
            session()->flash('success', 'Message resent successfully!');
            $this->message->refresh();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to resend message. Please try again.');
        }
    }

    public function getRecipientsProperty()
    {
        $query = $this->message->recipients()
            ->with('user')
            ->when($this->searchRecipients, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%'.$this->searchRecipients.'%')
                        ->orWhere('email', 'like', '%'.$this->searchRecipients.'%');
                });
            })
            ->when($this->readStatusFilter === 'read', function ($query) {
                $query->whereNotNull('read_at');
            })
            ->when($this->readStatusFilter === 'unread', function ($query) {
                $query->whereNull('read_at');
            });

        return $query->paginate(20);
    }

    public function render()
    {
        return view('livewire.teacher.messages.message-show', [
            'recipients' => $this->recipients,
            'readCount' => $this->message->recipients()->whereNotNull('read_at')->count(),
            'unreadCount' => $this->message->recipients()->whereNull('read_at')->count(),
            'totalCount' => $this->message->recipients()->count(),
        ]);
    }
}
