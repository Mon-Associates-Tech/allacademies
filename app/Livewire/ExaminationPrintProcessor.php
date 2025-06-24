<?php

namespace App\Livewire;

use App\Services\QuestionGenerator;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Request;
use Livewire\Component;

class ExaminationPrintProcessor extends Component
{
    public $academicSubject;
    public $data;
    public $team_id;
    public $creator_id;
    public $isPreview = false;
    public $shouldCreate = false;
    public function render(): View|Application|Factory|\Illuminate\View\View
    {
        return view('livewire.examination-print-processor', ['academicSubject' => $this->academicSubject, 'shouldCreate' => $this->shouldCreate]);
    }

    /**
     * @throws Exception
     */
    public function saveExamination()
    {
        if($this->isPreview) {
            (new QuestionGenerator())->createExamination($this->academicSubject, $this->data, $this->team_id, $this->creator_id,);
        }
        $this->js('window.print()');
        return $this->redirect(route('examinations.index', [
            'academic_subject' => $this->academicSubject,
            'academic_level' => $this->academicSubject->academicLevel,
            'academic_group' => $this->academicSubject->academicLevel->academicGroup,
        ]));
    }

}
