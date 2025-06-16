<?php

namespace App\Livewire;

use App\Services\QuestionGenerator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;

class ExaminationPrintProcessor extends Component
{
    public $academicSubject;
    public $data;
    public $team_id;
    public $creator_id;
    public function render(): View|Application|Factory|\Illuminate\View\View
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
        return $this->redirect(route('examinations.index', ['academic_subject' => $this->academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]));
    }

}
