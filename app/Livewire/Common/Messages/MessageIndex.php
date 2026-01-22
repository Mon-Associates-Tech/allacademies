<?php

namespace App\Livewire\Common\Messages;

use App\Models\Message;
use Livewire\Component;
use Livewire\WithPagination;

class MessageIndex extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = 'all';

    public $sortBy = 'created_at';

    public $sortDirection = 'desc';

    public $perPage = 15;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function deleteMessage($messageId)
    {
        $message = Message::findOrFail($messageId);

        // Check if user has permission to delete
        if ($message->sender_id !== auth()->id() && ! auth()->user()->hasRole(['admin', 'super-admin'])) {
            session()->flash('error', 'You do not have permission to delete this message.');

            return;
        }

        $message->delete();
        session()->flash('success', 'Message deleted successfully.');
    }

    public function duplicateMessage($messageId)
    {
        $message = Message::findOrFail($messageId);

        $newMessage = $message->replicate();
        $newMessage->subject = 'Copy of '.$message->subject;
        $newMessage->status = Message::STATUS_DRAFT;
        $newMessage->sent_at = null;
        $newMessage->scheduled_at = null;
        $newMessage->sender_id = auth()->id();
        $newMessage->save();

        // Copy attachments if any
        foreach ($message->attachments as $attachment) {
            $newAttachment = $attachment->replicate();
            $newAttachment->attachable_id = $newMessage->id;
            $newAttachment->save();
        }

        session()->flash('success', 'Message duplicated successfully.');

        return redirect()->route('admin.messages.edit', $newMessage);
    }

    public function getMessages()
    {
        $query = Message::with(['sender', 'recipients'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('subject', 'like', '%'.$this->search.'%')
                        ->orWhere('body', 'like', '%'.$this->search.'%')
                        ->orWhereHas('sender', function ($sq) {
                            $sq->where('name', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('status', $this->statusFilter);
            });

        return $query->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.common.messages.MessageIndex', [
            'messages' => $this->getMessages(),
            'statusCounts' => [
                'all' => Message::count(),
                'draft' => Message::where('status', Message::STATUS_DRAFT)->count(),
                'scheduled' => Message::where('status', Message::STATUS_SCHEDULED)->count(),
                'sent' => Message::where('status', Message::STATUS_SENT)->count(),
                'failed' => Message::where('status', Message::STATUS_FAILED)->count(),
            ],
        ]);
    }
}
