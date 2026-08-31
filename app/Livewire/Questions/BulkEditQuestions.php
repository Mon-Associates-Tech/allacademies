<?php

namespace App\Livewire\Questions;

use App\Models\AcademicTopic;
use App\Models\MultipleChoiceQuestion;
use Livewire\Component;
use Livewire\WithPagination;

class BulkEditQuestions extends Component
{
    use WithPagination;

    public $academicTopicId;
    public $academicGroupId;
    public $academicLevelId;
    public $academicSubjectId;

    public $search = '';

    // Holds the editable state for each question, keyed by Question ID
    public $states = [];

    protected $queryString = ['search' => ['except' => '']];

    public function mount($academicTopic, $academicGroup, $academicLevel, $academicSubject)
    {
        $this->academicTopicId = $academicTopic->id;
        $this->academicGroupId = $academicGroup->id;
        $this->academicLevelId = $academicLevel->id;
        $this->academicSubjectId = $academicSubject->id;

        // 1. Load base state from Database
        $this->loadStatesFromDb();

        // 2. Override with Session Draft if it exists (Persists across refresh)
        $draftKey = $this->getDraftKey();
        if (session()->has($draftKey)) {
            $draft = session()->get($draftKey);
            foreach ($draft as $id => $draftState) {
                if (isset($this->states[$id])) {
                    // Merge draft data, keeping the 'is_saved' flag from the draft
                    $this->states[$id] = array_merge($this->states[$id], $draftState);
                }
            }
        }
    }

    private function getDraftKey()
    {
        return "mcq_bulk_draft_{$this->academicTopicId}_user_" . auth()->id();
    }

    private function loadStatesFromDb()
    {
        $questions = MultipleChoiceQuestion::where('academic_topic_id', $this->academicTopicId)->get();
        foreach ($questions as $q) {
            $this->states[$q->id] = [
                'id' => $q->id,
                'question' => $q->question->down ?? '',
                'option_a' => $q->option_a->down ?? '',
                'option_b' => $q->option_b->down ?? '',
                'option_c' => $q->option_c->down ?? '',
                'option_d' => $q->option_d->down ?? '',
                'option_e' => $q->option_e->down ?? '',
                'answer' => strtoupper($q->answer ?? ''),
                'difficulty_level' => $q->difficulty_level ?? 'medium',
                'score' => $q->score ?? 1,
                'is_saved' => true,
            ];
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = MultipleChoiceQuestion::where('academic_topic_id', $this->academicTopicId)->latest('created_at');

        if ($this->search) {
            $query->whereRaw('LOWER(question) LIKE ?', ['%'.strtolower($this->search).'%']);
        }

        $questions = $query->paginate(15);
        $topic = AcademicTopic::with('academicSubject.academicLevel.academicGroup')->find($this->academicTopicId);

        return view('livewire.questions.bulk-edit-questions', [
            'questions' => $questions,
            'academicTopic' => $topic,
        ]);
    }

    /**
     * SESSION DRAFT PERSISTENCE
     * Triggers automatically on debounce. Saves to PHP Session, NOT the database.
     */
    public function updatedStates($value, $key)
    {
        $parts = explode('.', $key);
        $questionId = $parts[0];

        // Mark as unsaved in UI
        if (isset($this->states[$questionId])) {
            $this->states[$questionId]['is_saved'] = false;
        }

        // Save entire state to PHP Session
        session()->put($this->getDraftKey(), $this->states);
    }

    public function saveSingle($questionId)
    {
        $saved = $this->saveToDb($questionId);

        if ($saved) {
            $draft = session()->get($this->getDraftKey(), []);
            unset($draft[$questionId]);
            session()->put($this->getDraftKey(), $draft);

            $this->dispatch('show-toast', message: 'Question saved successfully', type: 'success');
        } else {
            $this->dispatch('show-toast', message: 'No changes to save', type: 'info');
        }
    }

    public function saveAll()
    {
        $count = 0;
        $draft = session()->get($this->getDraftKey(), []);

        foreach ($this->states as $id => $state) {
            // Only increment count if the question was actually modified
            if ($this->saveToDb($id)) {
                $count++;
                unset($draft[$id]); // Clear from draft
            }
        }

        session()->put($this->getDraftKey(), $draft);

        if ($count > 0) {
            $msg = $count === 1 ? '1 question saved' : "{$count} questions saved";
            $this->dispatch('show-toast', message: $msg, type: 'success');
        } else {
            $this->dispatch('show-toast', message: 'No changes to save', type: 'success');
        }
    }

    /**
     * EXPLICIT DATABASE SAVE
     * Uses isDirty() to ensure we only count and save questions that actually changed.
     */
    private function saveToDb($questionId): bool
    {
        $state = $this->states[$questionId] ?? null;
        if (!$state) return false;

        $question = MultipleChoiceQuestion::find($questionId);
        if (!$question) return false;

        // Assign attributes to the model to trigger the Mark cast
        $question->question         = $state['question'];
        $question->option_a         = $state['option_a'];
        $question->option_b         = $state['option_b'];
        $question->option_c         = $state['option_c'];
        $question->option_d         = $state['option_d'];
        $question->option_e         = $state['option_e'];
        $question->answer           = strtolower($state['answer']);
        $question->difficulty_level = $state['difficulty_level'];
        $question->score            = $state['score'];

        // Check if anything actually changed compared to the database
        if ($question->isDirty()) {
            $question->save();
            $this->states[$questionId]['is_saved'] = true;
            return true; // Actually saved
        }

        // If not dirty, it's already in sync with the database
        $this->states[$questionId]['is_saved'] = true; // Still mark as saved in UI to turn the border green
        return false; // No changes made, so don't count it
    }
}
