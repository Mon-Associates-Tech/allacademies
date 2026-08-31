{{-- resources/views/timetable/rooms.blade.php --}}
<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
        <x-timetable::tabs active="rooms" />
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
        <livewire:timetable.rooms />
    </div>
</x-layouts.app>
