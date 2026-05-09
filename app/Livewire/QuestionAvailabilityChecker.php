<?php

namespace App\Livewire;

use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\AcademicSubtopic;
use App\Models\AcademicTopic;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class QuestionAvailabilityChecker extends Component
{
    public $academicGroups = [];
    public $academicLevels = [];
    public $academicSubjects = [];
    public $academicTopics = [];

    public $selectedGroup = null;
    public $selectedLevel = null;
    public $selectedSubject = null;
    public $selectedTopics = [];
    public $questionType = 'multiple_choice_questions';
    public $requiredCount = 10;

    public $result = null;
    public $loading = false;
    public $idMap = null;

    public function mount()
    {
        $this->academicGroups = AcademicGroup::all();
        $this->loadIdMap();
    }

    public function loadIdMap()
    {
        $mapPath = storage_path('app/academic_id_map.json');
        if (file_exists($mapPath)) {
            $this->idMap = json_decode(file_get_contents($mapPath), true);
        }
    }

    public function generateIdMap()
    {
        $this->loading = true;
        \Artisan::call('academic:id-map');
        $this->loadIdMap();
        $this->loading = false;
        session()->flash('message', 'ID Map generated successfully!');
    }

    public function updatedSelectedGroup($value)
    {
        $this->selectedLevel = null;
        $this->selectedSubject = null;
        $this->selectedTopics = [];
        $this->academicLevels = [];
        $this->academicSubjects = [];
        $this->academicTopics = [];
        $this->result = null;

        if ($value) {
            $this->academicLevels = AcademicLevel::where('academic_group_id', $value)->get();
        }
    }

    public function updatedSelectedLevel($value)
    {
        $this->selectedSubject = null;
        $this->selectedTopics = [];
        $this->academicSubjects = [];
        $this->academicTopics = [];
        $this->result = null;

        if ($value) {
            $this->academicSubjects = AcademicSubject::where('academic_level_id', $value)->get();
        }
    }

    public function updatedSelectedSubject($value)
    {
        $this->selectedTopics = [];
        $this->academicTopics = [];
        $this->result = null;

        if ($value) {
            $this->academicTopics = AcademicTopic::where('academic_subject_id', $value)
                ->select('id', 'name')
                ->get();
        }
    }

    public function checkAvailability()
    {
        $this->validate([
            'selectedSubject' => 'required',
            'questionType' => 'required',
            'requiredCount' => 'required|integer|min:1',
        ]);

        $this->loading = true;
        $this->result = null;

        try {
            $subject = AcademicSubject::findOrFail($this->selectedSubject);
            $topicIds = $this->selectedTopics;

            // If no topics specified, get all topics for the subject
            if (empty($topicIds)) {
                $topicIds = AcademicTopic::where('academic_subject_id', $subject->id)
                    ->pluck('id')
                    ->toArray();
            }

            $availability = $this->performAvailabilityCheck(
                $this->questionType,
                $topicIds,
                [],
                $this->requiredCount
            );

            $this->result = [
                'subject' => [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'code' => $subject->code,
                ],
                'question_type' => $this->questionType,
                'required_count' => $this->requiredCount,
                'available_count' => $availability['available_count'],
                'sufficient' => $availability['sufficient'],
                'breakdown' => $availability['breakdown'],
            ];
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }

        $this->loading = false;
    }

    private function performAvailabilityCheck(
        string $table,
        array $topicIds,
        array $subtopicIds,
        int $requiredCount
    ): array {
        $breakdown = [
            'by_topic' => [],
            'by_subtopic' => [],
        ];

        $totalAvailable = 0;

        // Check subtopic availability
        if (!empty($subtopicIds)) {
            foreach ($subtopicIds as $subtopicId) {
                $subtopic = AcademicSubtopic::find($subtopicId);
                if (!$subtopic) {
                    continue;
                }

                $count = DB::table($table)
                    ->where('academic_subtopic_id', $subtopicId)
                    ->count();

                $breakdown['by_subtopic'][] = [
                    'id' => $subtopic->id,
                    'name' => $subtopic->name,
                    'available' => $count,
                ];

                $totalAvailable += $count;
            }
        }

        // Check topic availability
        if (!empty($topicIds)) {
            foreach ($topicIds as $topicId) {
                $topic = AcademicTopic::find($topicId);
                if (!$topic) {
                    continue;
                }

                // Count all questions for this topic
                $count = DB::table($table)
                    ->where('academic_topic_id', $topicId)
                    ->count();

                // If we're checking specific subtopics, don't double count
                if (empty($subtopicIds)) {
                    $totalAvailable += $count;
                }

                $breakdown['by_topic'][] = [
                    'id' => $topic->id,
                    'name' => $topic->name,
                    'available' => $count,
                ];
            }
        }

        return [
            'available_count' => $totalAvailable,
            'sufficient' => $totalAvailable >= $requiredCount,
            'breakdown' => $breakdown,
        ];
    }

    public function render()
    {
        return view('livewire.question-availability-checker');
    }
}
