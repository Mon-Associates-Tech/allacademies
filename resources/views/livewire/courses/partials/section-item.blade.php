{{-- Section Item for Course Player Sidebar --}}
@php
    $sectionProgress = $this->getSectionProgressData($section);
    $isComplete = $sectionProgress['is_complete'] ?? false;
    $paddingLeft = ($depth * 1) + 0.75;
@endphp

<div class="border-b border-gray-100 dark:border-gray-700 last:border-b-0" wire:key="section-{{ $section->id }}">
    {{-- Section Header --}}
    <div class="flex items-center gap-2 py-2 px-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors" style="padding-left: {{ $paddingLeft }}rem;">
        {{-- Completion Indicator --}}
        <div class="flex-shrink-0">
            @if($isComplete)
                <div class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-900 flex items-center justify-center">
                    <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            @else
                <div class="w-5 h-5 rounded-full border-2 border-gray-300 dark:border-gray-600"></div>
            @endif
        </div>

        {{-- Section Title --}}
        <span class="text-sm text-gray-700 dark:text-gray-300 truncate flex-1">
            {{ $section->title }}
        </span>
    </div>

    {{-- Section Contents --}}
    @foreach($section->contents()->orderBy('order')->get() as $content)
        @php
            $contentProgress = $this->getContentProgressData($content);
            $isContentComplete = $contentProgress['is_completed'] ?? false;
            $isCurrentContent = $currentContent && $currentContent->id === $content->id;
        @endphp
        <button
            wire:click="selectContent({{ $content->id }})"
            class="w-full flex items-center gap-2 py-2 px-3 text-left transition-colors
                {{ $isCurrentContent ? 'bg-indigo-50 dark:bg-indigo-900/30 border-l-2 border-indigo-600' : 'hover:bg-gray-50 dark:hover:bg-gray-700/50' }}"
            style="padding-left: {{ $paddingLeft + 1.5 }}rem;"
            wire:key="content-{{ $content->id }}"
        >
            {{-- Content Type Icon --}}
            <div class="flex-shrink-0">
                @if($isContentComplete)
                    <div class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-900 flex items-center justify-center">
                        <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                @else
                    @switch($content->type)
                        @case('video')
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            @break
                        @case('audio')
                            <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                            </svg>
                            @break
                        @case('text')
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            @break
                        @case('quiz')
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            @break
                        @default
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                            </svg>
                    @endswitch
                @endif
            </div>

            {{-- Content Title --}}
            <span class="text-sm truncate flex-1 {{ $isCurrentContent ? 'text-indigo-700 dark:text-indigo-300 font-medium' : 'text-gray-600 dark:text-gray-400' }}">
                {{ $content->title }}
            </span>

            {{-- Duration (if applicable) --}}
            @if($content->duration_seconds)
                <span class="text-xs text-gray-400 dark:text-gray-500 flex-shrink-0">
                    {{ floor($content->duration_seconds / 60) }}:{{ str_pad($content->duration_seconds % 60, 2, '0', STR_PAD_LEFT) }}
                </span>
            @endif
        </button>
    @endforeach

    {{-- Nested Subsections --}}
    @foreach($section->subsections()->orderBy('order')->get() as $childSection)
        @include('livewire.courses.partials.section-item', ['section' => $childSection, 'depth' => $depth + 1])
    @endforeach
</div>
