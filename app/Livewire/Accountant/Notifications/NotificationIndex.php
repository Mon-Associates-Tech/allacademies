<?php

namespace App\Livewire\Accountant\Notifications;

use App\Models\Message;
use Livewire\Component;
use Livewire\WithPagination;

class NotificationIndex extends Component
{
    use WithPagination;

    public string $tab = 'sent'; // sent|draft|scheduled

    public string $search = '';

    public function updatedTab(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function deleteDraft(int $id): void
    {
        $message = Message::where('sender_id', auth()->id())
            ->where('status', Message::STATUS_DRAFT)
            ->findOrFail($id);

        $message->delete();
        session()->flash('success', 'Draft deleted.');
    }

    public function cancelScheduled(int $id): void
    {
        $message = Message::where('sender_id', auth()->id())
            ->where('status', Message::STATUS_SCHEDULED)
            ->findOrFail($id);

        $message->update(['status' => Message::STATUS_DRAFT]);
        session()->flash('success', 'Scheduled notification moved to drafts.');
    }

    public function render()
    {
        $query = Message::where('sender_id', auth()->id())
            ->with(['template', 'recipients'])
            ->when($this->search, fn ($q) => $q->where('subject', 'like', "%{$this->search}%"))
            ->latest();

        $messages = match ($this->tab) {
            'draft'     => (clone $query)->drafts()->paginate(15),
            'scheduled' => (clone $query)->scheduled()->paginate(15),
            default     => (clone $query)->sent()->paginate(15),
        };

        $school = auth()->user()->school;
        $baseVars = [
            'school_name'    => $school?->name ?? config('app.name'),
            'currency'       => $school?->currency ?? 'GHS',
            'recipient_name' => '…',
            'student_name'   => '…',
            'term_name'      => '…',
            'balance'        => '…',
            'due_date'       => '…',
            'total_amount'   => '…',
            'amount_paid'    => '…',
            'event_title'    => '…',
            'event_date'     => '…',
            'event_venue'    => '…',
            'message_body'   => '…',
        ];

        return view('livewire.accountant.notifications.notification-index', [
            'messages' => $messages,
            'baseVars' => $baseVars,
        ]);
    }
}
