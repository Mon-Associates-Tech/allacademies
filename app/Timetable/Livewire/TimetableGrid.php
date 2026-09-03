<?php
// app/Timetable/Livewire/TimetableGrid.php

namespace App\Timetable\Livewire;

use App\Models\AcademicPeriod;
use App\Timetable\Models\Room;
use App\Timetable\Models\TimeSlot;
use App\Timetable\Models\TimetableEntry;
use App\Timetable\Services\ConflictDetectionService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class TimetableGrid extends Component
{
    public ?int $academicPeriodId = null;
    public ?int $academicLevelFilter = null;
    public ?int $teacherFilter = null;

    public bool $showModal = false;
    public ?int $editingEntryId = null;
    public array $form = [
        'academic_level_id' => null,
        'academic_subject_id' => null,
        'teacher_id' => null,
        'room_id' => null,
        'time_slot_id' => null,
        'day_of_week' => 'monday',
    ];

    public array $conflictWarnings = [];

    public function mount(): void
    {
        $this->academicPeriodId = AcademicPeriod::getCurrentPeriodForSchool($this->schoolId())?->id;
    }

    #[Computed]
    public function academicPeriods()
    {
        return AcademicPeriod::forSchool($this->schoolId())
            ->currentOrUpcoming()
            ->orderByDesc('start_date')
            ->get();
    }

    #[Computed]
    public function timeSlots()
    {
        return TimeSlot::forSchool($this->schoolId())->get();
    }

    #[Computed]
    public function rooms()
    {
        return Room::forSchool($this->schoolId())->where('is_active', true)->get();
    }

    #[Computed]
    public function academicLevels()
    {
        return \App\Models\AcademicLevel::forSchool($this->schoolId())->orderBy('name')->get();
    }

    #[Computed]
    public function academicSubjects()
    {
        return \App\Models\AcademicSubject::whereHas('academicLevel', fn ($q) => $q->forSchool($this->schoolId()))
            ->orderBy('name')->get();
    }

    #[Computed]
    public function teachers()
    {
        return \App\Models\Teacher::where('school_id', $this->schoolId())->with('user')->get();
    }

    #[Computed]
    public function entries()
    {
        if (! $this->academicPeriodId) {
            return collect();
        }

        $query = TimetableEntry::query()
            ->forSchool($this->schoolId())
            ->forPeriod($this->academicPeriodId)
            ->with(['academicSubject', 'teacher.user', 'room', 'timeSlot']);

        if ($this->academicLevelFilter) {
            $query->forClass($this->academicLevelFilter);
        }
        if ($this->teacherFilter) {
            $query->forTeacher($this->teacherFilter);
        }

        return $query->get()->groupBy(['day_of_week', 'time_slot_id']);
    }

    public function updatedAcademicPeriodId(): void
    {
        unset($this->entries);
    }

    public function openCreate(string $dayOfWeek, int $timeSlotId): void
    {
        $this->authorize('create', TimetableEntry::class);

        $this->resetForm();
        $this->form['day_of_week'] = $dayOfWeek;
        $this->form['time_slot_id'] = $timeSlotId;
        if ($this->academicLevelFilter) {
            $this->form['academic_level_id'] = $this->academicLevelFilter;
        }
        $this->editingEntryId = null;
        $this->showModal = true;
    }

    public function openEdit(int $entryId): void
    {
        $entry = TimetableEntry::findOrFail($entryId);
        $this->authorize('update', $entry);

        $this->form = $entry->only([
            'academic_level_id', 'academic_subject_id', 'teacher_id',
            'room_id', 'time_slot_id', 'day_of_week',
        ]);
        $this->editingEntryId = $entry->id;
        $this->showModal = true;
    }

    public function save(ConflictDetectionService $conflicts): void
    {
        $this->validate([
            'form.academic_level_id' => 'required|exists:academic_levels,id',
            'form.academic_subject_id' => 'required|exists:academic_subjects,id',
            'form.teacher_id' => 'required|exists:teachers,id',
            'form.room_id' => 'required|exists:rooms,id',
            'form.time_slot_id' => 'required|exists:time_slots,id',
            'form.day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday',
        ]);

        if (! $this->academicPeriodId) {
            $this->addError('form.academic_level_id', 'Select an academic period first.');
            return;
        }

        $schoolId = $this->schoolId();

        if ($this->editingEntryId) {
            $entry = TimetableEntry::findOrFail($this->editingEntryId);
            $this->authorize('update', $entry);
        } else {
            $this->authorize('create', TimetableEntry::class);
        }

        $result = $conflicts->check(
            schoolId: $schoolId,
            academicPeriodId: $this->academicPeriodId,
            teacherId: (int) $this->form['teacher_id'],
            roomId: (int) $this->form['room_id'],
            academicLevelId: (int) $this->form['academic_level_id'],
            timeSlotId: (int) $this->form['time_slot_id'],
            dayOfWeek: $this->form['day_of_week'],
            excludeEntryId: $this->editingEntryId,
        );

        $activeConflicts = array_filter($result);
        if (! empty($activeConflicts)) {
            $this->conflictWarnings = array_keys($activeConflicts);
            return;
        }

        $payload = array_merge($this->form, [
            'school_id' => $schoolId,
            'academic_period_id' => $this->academicPeriodId,
            'modified_by' => Auth::id(),
        ]);

        if ($this->editingEntryId) {
            $entry = TimetableEntry::findOrFail($this->editingEntryId);
            $before = $entry->only(array_keys($this->form));
            $entry->update($payload);

            $entry->logActivity('update', 'Timetable Entry Updated', 'academic', [
                'before' => $before,
                'after' => $entry->only(array_keys($this->form)),
            ]);
        } else {
            $payload['added_by'] = Auth::id();
            $entry = TimetableEntry::create($payload);

            $entry->logActivity('create', 'Timetable Entry Created', 'academic', [
                'entry' => $entry->only(array_keys($this->form)),
            ]);
        }

        $this->showModal = false;
        $this->resetForm();
        unset($this->entries);
    }

    public function delete(int $entryId): void
    {
        $entry = TimetableEntry::findOrFail($entryId);
        $this->authorize('delete', $entry);

        $snapshot = $entry->only(['academic_level_id', 'academic_subject_id', 'teacher_id', 'room_id', 'time_slot_id', 'day_of_week']);
        $entry->delete();

        $entry->logActivity('delete', 'Timetable Entry Deleted', 'academic', ['entry' => $snapshot]);

        unset($this->entries);
    }

    protected function resetForm(): void
    {
        $this->form = [
            'academic_level_id' => null,
            'academic_subject_id' => null,
            'teacher_id' => null,
            'room_id' => null,
            'time_slot_id' => null,
            'day_of_week' => 'monday',
        ];
        $this->conflictWarnings = [];
    }

    protected function schoolId(): ?int
    {
        $user = Auth::user();
        return $user->canAccessCrossSchool()
            ? (session('current_school_id') ?? $user->school_id)
            : $user->school_id;
    }

    public function render()
    {
        return view('timetable.livewire.grid');
    }
}
