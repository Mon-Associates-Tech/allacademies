<x-layouts.app pageName="My Teachers">
    <x-slot name="title">My Teachers</x-slot>

    <div class="space-y-6">
        <p class="text-sm text-gray-600 dark:text-gray-300">Teachers assigned to your classes and subjects.</p>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse($teachers as $teacher)
                <article class="rounded-lg bg-white p-5 shadow dark:bg-gray-800">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $teacher->user?->name ?? 'Teacher' }}</h2>
                    @if($teacher->department)<p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ $teacher->department }}</p>@endif
                    @if($teacher->specialization)<p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ $teacher->specialization }}</p>@endif
                    @if($teacher->subjects->isNotEmpty())
                        <p class="mt-3 text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Subjects</p>
                        <p class="mt-1 text-sm text-gray-700 dark:text-gray-200">{{ $teacher->subjects->pluck('name')->join(', ') }}</p>
                    @endif
                </article>
            @empty
                <div class="rounded-lg bg-white p-8 text-sm text-gray-500 shadow dark:bg-gray-800 md:col-span-2 xl:col-span-3">No teachers are currently assigned to your student profile.</div>
            @endforelse
        </div>
    </div>
</x-layouts.app>