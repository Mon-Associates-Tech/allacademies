<?php

namespace App\Livewire\Administrators;

use App\Models\LoginActivity;
use Livewire\Component;
use Livewire\WithPagination;

class UserLoginLog extends Component
{
    use WithPagination;

    public $searchTerm = '';

    public $userId = null;

    public function render()
    {
        $activities = LoginActivity::with('user')
            ->when($this->searchTerm, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%'.$this->searchTerm.'%');
                })->orWhere('action', 'like', '%'.$this->searchTerm.'%');
            })
            ->when($this->searchTerm, function ($query) {
                $query->where('device_type', 'like', '%'.$this->searchTerm.'%')
                    ->orWhere('platform', 'like', '%'.$this->searchTerm.'%')
                    ->orWhere('browser', 'like', '%'.$this->searchTerm.'%')
                    ->orWhere('ip_address', 'like', '%'.$this->searchTerm.'%')
                    ->orWhere('country', 'like', '%'.$this->searchTerm.'%');
            })
            ->latest()
            ->paginate(15);

        return view('livewire.administrators.user-logins', [
            'activities' => $activities,
        ]);
    }
}
