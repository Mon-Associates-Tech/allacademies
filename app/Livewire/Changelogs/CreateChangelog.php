<?php

namespace App\Livewire\Changelogs;

use App\Models\Changelog;
use Livewire\Component;

class CreateChangelog extends Component
{
    public $title;

    public $task_name;

    public $task_description;

    public $additional_info;

    public $completed_items = [''];

    protected $rules = [
        'title' => 'required|string|max:255',
        'task_name' => 'required|string|max:255',
        'task_description' => 'required|string',
        'additional_info' => 'nullable|string',
        'completed_items' => 'required|array|min:1',
        'completed_items.*' => 'required|string|max:255',
    ];

    public function addCompletedItem()
    {
        $this->completed_items[] = '';
    }

    public function removeCompletedItem($index)
    {
        unset($this->completed_items[$index]);
        $this->completed_items = array_values($this->completed_items);
    }

    public function save()
    {
        $this->validate();

        Changelog::create([
            'title' => $this->title,
            'task_name' => $this->task_name,
            'task_description' => $this->task_description,
            'additional_info' => $this->additional_info,
            'completed_items' => array_values(array_filter($this->completed_items)),
        ]);

        session()->flash('message', 'Changelog entry created successfully.');

        $this->reset();
        $this->completed_items = [''];

        $this->dispatch('changelogAdded');
    }

    public function render()
    {
        return view('livewire.changelogs.create-changelog');
    }
}
