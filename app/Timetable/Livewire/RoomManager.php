<?php
// app/Timetable/Livewire/RoomManager.php

namespace App\Timetable\Livewire;

use App\Timetable\Models\Room;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class RoomManager extends Component
{
    public bool $showModal = false;
    public ?int $editingRoomId = null;

    public array $form = [
        'name' => '',
        'type' => 'classroom',
        'capacity' => null,
        'is_active' => true,
    ];

    #[Computed]
    public function rooms()
    {
        return Room::forSchool($this->schoolId())->orderBy('name')->get();
    }

    public function openCreate(): void
    {
        $this->authorize('create', Room::class);
        $this->resetForm();
        $this->editingRoomId = null;
        $this->showModal = true;
    }

    public function openEdit(int $roomId): void
    {
        $room = Room::findOrFail($roomId);
        $this->authorize('update', $room);

        $this->form = $room->only(['name', 'type', 'capacity', 'is_active']);
        $this->editingRoomId = $room->id;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'form.name' => 'required|string|max:255',
            'form.type' => 'required|in:classroom,lab,hall',
            'form.capacity' => 'nullable|integer|min:1',
        ]);

        $schoolId = $this->schoolId();

        if ($this->editingRoomId) {
            $room = Room::findOrFail($this->editingRoomId);
            $this->authorize('update', $room);

            $before = $room->only(array_keys($this->form));
            $room->update(array_merge($this->form, ['modified_by' => Auth::id()]));

            $room->logActivity('update', 'Room Updated', 'academic', [
                'before' => $before,
                'after' => $room->only(array_keys($this->form)),
            ]);
        } else {
            $this->authorize('create', Room::class);

            $room = Room::create(array_merge($this->form, [
                'school_id' => $schoolId,
                'added_by' => Auth::id(),
            ]));

            $room->logActivity('create', 'Room Created', 'academic', [
                'room' => $room->only(array_keys($this->form)),
            ]);
        }

        $this->showModal = false;
        $this->resetForm();
        unset($this->rooms);
    }

    public function delete(int $roomId): void
    {
        $room = Room::findOrFail($roomId);
        $this->authorize('delete', $room);

        // Block deletion if the room is still referenced by the live timetable,
        // rather than silently cascading and leaving orphaned-looking entries.
        if ($room->timetableEntries()->exists()) {
            $this->addError('form.name', 'This room is used in the timetable and cannot be deleted. Deactivate it instead.');
            return;
        }

        $snapshot = $room->only(['name', 'type', 'capacity']);
        $room->delete();

        $room->logActivity('delete', 'Room Deleted', 'academic', ['room' => $snapshot]);

        unset($this->rooms);
    }

    protected function resetForm(): void
    {
        $this->form = [
            'name' => '',
            'type' => 'classroom',
            'capacity' => null,
            'is_active' => true,
        ];
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
        return view('timetable.livewire.rooms');
    }
}
