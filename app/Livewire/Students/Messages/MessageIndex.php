<?php

namespace App\Livewire\Students\Messages;

use App\Models\Message;
use Livewire\Component;

class MessageIndex extends Component
{
    public $search = '';
    public $perPage = 10;

    protected $queryString = ['search'];

    public function render()
    {
        $messages = Message::where('sender_id', auth()->id())
            ->orWhereHas('recipients', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->when($this->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('subject', 'like', '%' . $search . '%')
                      ->orWhere('body', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.students.messages.index', [
            'messages' => $messages
        ]);
    }
}
