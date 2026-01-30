<?php

namespace App\Livewire\Changelogs;

use App\Models\Changelog;
use Livewire\Component;
use Livewire\WithPagination;

class ChangelogList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $showModal = false;

    protected $listeners = ['changelogAdded' => '$refresh'];

    public function render()
    {
        $changelogs = Changelog::orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.changelogs.changelog-list', [
            'changelogs' => $changelogs,
        ]);
    }

    public function showCreateModal()
    {
        $this->showModal = true;
    }

    public function closeCreateModal()
    {
        $this->showModal = false;
    }
}
