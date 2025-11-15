<?php

namespace App\Livewire\SchoolSettings;

use App\Models\School;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class LetterheadSettings extends Component
{
    public $school;
    public $selectedTemplate;
    public $previewTemplate;

    public $availableTemplates = [
        'classic' => [
            'name' => 'Classic',
            'description' => 'Traditional professional letterhead with centered logo and information',
            'preview_color' => '#1e40af',
        ],
        'modern' => [
            'name' => 'Modern',
            'description' => 'Contemporary gradient design with bold typography',
            'preview_color' => '#667eea',
        ],
        'minimal' => [
            'name' => 'Minimal',
            'description' => 'Clean and simple design with centered content',
            'preview_color' => '#111827',
        ],
        'elegant' => [
            'name' => 'Elegant',
            'description' => 'Sophisticated design with bordered layout and serif fonts',
            'preview_color' => '#1f2937',
        ],
        'corporate' => [
            'name' => 'Corporate',
            'description' => 'Professional dark theme with structured layout',
            'preview_color' => '#1e293b',
        ],
        'academic' => [
            'name' => 'Academic',
            'description' => 'Traditional academic style with double border and purple accents',
            'preview_color' => '#7c3aed',
        ],
        'colourful' => [
            'name' => 'Colorful',
            'description' => 'Vibrant multi-color gradient design for a cheerful look',
            'preview_color' => '#f59e0b',
        ],
        'professional' => [
            'name' => 'Professional',
            'description' => 'Business-style layout with right-aligned information',
            'preview_color' => '#0891b2',
        ],
        'executive' => [
            'name' => 'Executive',
            'description' => 'Premium dark gradient design with blue highlights',
            'preview_color' => '#111827',
        ],
    ];

    public function mount()
    {
        $user = Auth::user();
        $schoolId = getSchoolId();
        $this->school = School::find($schoolId);

        if ($this->school) {
            $this->selectedTemplate = $this->school->letterhead_template ?? 'classic';
            $this->previewTemplate = $this->selectedTemplate;
        } else {
            session()->flash('error', 'No school associated with your account.');
        }
    }

    public function previewLetterhead($template)
    {
        $this->previewTemplate = $template;
    }

    public function saveTemplate()
    {
        if (!$this->school) {
            session()->flash('error', 'School not found.');
            return;
        }

        $this->school->update([
            'letterhead_template' => $this->selectedTemplate
        ]);

        session()->flash('success', 'Letterhead template updated successfully!');
    }

    public function render()
    {
        return view('livewire.school-settings.letterhead-settings');
    }
}
