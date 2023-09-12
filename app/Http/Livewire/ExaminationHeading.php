<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ExaminationHeading extends Component
{
    public $academicSubject;
    public $academicLevel;
    public $metaData;
    public $heading;
    public $heading_type = 0;
    public $title;
    public $date;
    public $start;
    public $end;
    public $instructions;
    public $examiners;
    
    public function mount($academicSubject, $academicLevel, $metaData, $package)
    {
        $this->academicSubject = $academicSubject;
        $this->academicLevel = $academicLevel;
        $this->metaData = $metaData;
        $this->package = $package;
    }

    public function render()
    {
        return view('livewire.examination-heading');
    }
}
