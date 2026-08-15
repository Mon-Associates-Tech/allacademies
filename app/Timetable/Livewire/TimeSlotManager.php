<?php
// app/Timetable/Livewire/TimeSlotManager.php

namespace App\Timetable\Livewire;

use App\Timetable\Models\TimeSlot;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class TimeSlotManager extends Component
{
    public bool $showModal = false;
    public ?int $editingSlotId = null;

    public array $form = [
        'label' => '',
        'starts_at' => '',
        'ends_at' => '',
        'order' => 0,
        'is_break' => false,
    ];

    #[Computed]
    public function timeSlots()
    {
        return TimeSlot::forSchool($this->schoolId())->get(); // forSchool already orders by 'order'
    }

    public function openCreate(): void
    {
        $this->authorize('create', TimeSlot::class);
        $this->resetForm();
        $this->form['order'] = ($this->timeSlots->max('order') ?? 0) + 1;
        $this->editingSlotId = null;
        $this->showModal = true;
    }

    public function openEdit(int $slotId): void
    {
        $slot = TimeSlot::findOrFail($slotId);
        $this->authorize('update', $slot);

        $this->form = [
            'label' => $slot->label,
            'starts_at' => \Carbon\Carbon::parse($slot->starts_at)->format('H:i'),
            'ends_at' => \Carbon\Carbon::parse($slot->ends_at)->format('H:i'),
            'order' => $slot->order,
            'is_break' => $slot->is_break,
        ];
        $this->editingSlotId = $slot->id;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'form.label' => 'required|string|max:255',
            'form.starts_at' => 'required|date_format:H:i',
            'form.ends_at' => 'required|date_format:H:i|after:form.starts_at',
            'form.order' => 'required|integer|min:0',
        ]);

        $schoolId = $this->schoolId();

        if ($this->editingSlotId) {
            $slot = TimeSlot::findOrFail($this->editingSlotId);
            $this->authorize('update', $slot);

            $before = $slot->only(array_keys($this->form));
            $slot->update($this->form);

            $slot->logActivity('update', 'Time Slot Updated', 'academic', [
                'before' => $before,
                'after' => $slot->only(array_keys($this->form)),
            ]);
        } else {
            $this->authorize('create', TimeSlot::class);

            $slot = TimeSlot::create(array_merge($this->form, ['school_id' => $schoolId]));

            $slot->logActivity('create', 'Time Slot Created', 'academic', [
                'slot' => $slot->only(array_keys($this->form)),
            ]);
        }

        $this->showModal = false;
        $this->resetForm();
        unset($this->timeSlots);
    }

    public function delete(int $slotId): void
    {
        $slot = TimeSlot::findOrFail($slotId);
        $this->authorize('delete', $slot);

        if ($slot->timetableEntries()->exists()) {
            $this->addError('form.label', 'This time slot is used in the timetable and cannot be deleted.');
            return;
        }

        $snapshot = $slot->only(['label', 'starts_at', 'ends_at']);
        $slot->delete();

        $slot->logActivity('delete', 'Time Slot Deleted', 'academic', ['slot' => $snapshot]);

        unset($this->timeSlots);
    }

    protected function resetForm(): void
    {
        $this->form = ['label' => '', 'starts_at' => '', 'ends_at' => '', 'order' => 0, 'is_break' => false];
    }

    protected function schoolId(): int
    {
        $user = Auth::user();
        return $user->canAccessCrossSchool()
            ? (session('current_school_id') ?? $user->school_id)
            : $user->school_id;
    }

    public function render()
    {
        return view('timetable.livewire.time-slots');
    }
}
