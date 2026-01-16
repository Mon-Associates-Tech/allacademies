<?php

namespace App\Livewire\Common;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserPreferences extends Component
{
    public $preferences = [];

    public $themes = [
        'light' => 'Light',
        'dark' => 'Dark',
        'system' => 'System Default',
    ];

    public $fonts = [
        'sans' => 'Sans Serif',
        'serif' => 'Serif',
        'mono' => 'Monospace',
    ];

    public function mount()
    {
        $this->loadPreferences();
    }

    public function loadPreferences()
    {
        $userPreferences = Auth::user()->preferences->pluck('value', 'key')->toArray();

        // Set default values if not set
        $this->preferences = array_merge([
            'theme' => 'system',
            'font' => 'sans',
            'newsletter' => 'false',
            'assignment_notifications' => 'true',
            'quiz_notifications' => 'true',
            'exam_notifications' => 'true',
            'grade_notifications' => 'true',
        ], $userPreferences);
    }

    public function save()
    {
        foreach ($this->preferences as $key => $value) {
            Auth::user()->preferences()->updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        session()->flash('message', 'Preferences saved successfully.');
    }

    public function render()
    {
        return view('livewire.common.user-preferences');
    }
}
