<?php

namespace App\Livewire;

use App\Services\QuestionGenerator;
use Livewire\Component;

class ExaminationPrintProcessor extends Component
{
    public $academicSubject;
    public $data;
    public $team_id;
    public $creator_id;
    public function render()
    {
        return view('livewire.examination-print-processor', ['academicSubject' => $this->academicSubject]);
    }

    /**
     * @throws \Exception
     */
    public function saveExamination()
    {
        (new QuestionGenerator())->createExamination($this->academicSubject, $this->data, $this->team_id, $this->creator_id,);
        $this->js('window.print()');
        return $this->redirect(route('academic-subjects.examinations.index', ['academic_subject' => $this->academicSubject]));
    }

}
