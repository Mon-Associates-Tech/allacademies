<?php

namespace App\Livewire\Teachers;

use Livewire\Component;

class VirtualClassroom extends Component
{
    public function startLiveSession()
    {
        return redirect()->route('teachers.classroom.create');
    }

    public function uploadRecordedSession()
    {
        // You can create a separate component for uploading recorded sessions
        return redirect()->route('teachers.classroom.upload');
    }

    public function render()
    {
        return view('livewire.teachers.virtual-classroom');
    }
}
