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
        $changedPreferences = [];

        foreach ($this->preferences as $key => $value) {
            // Track what changed
            $oldValue = Auth::user()->preferences()->where('key', $key)->first()?->value;
            if ($oldValue !== $value) {
                $changedPreferences[$key] = [
                    'old' => $oldValue,
                    'new' => $value,
                ];
            }

            Auth::user()->preferences()->updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Log activity if there were any changes
        if (! empty($changedPreferences)) {
            Auth::user()->logActivity('update', 'User Preferences Updated', 'user_preferences', [
                'preferences_changed' => array_keys($changedPreferences),
                'updated_by' => auth()->user()?->name ?? 'Unknown',
            ]);
        }

        session()->flash('message', 'Preferences saved successfully.');
    }

    public function render()
    {
        return view('livewire.common.user-preferences');
    }
}
