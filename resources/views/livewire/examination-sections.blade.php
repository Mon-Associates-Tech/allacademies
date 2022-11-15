@php
    $options = [
        ['label' => 'MC', 'value' => 'multiple_choice_questions'],
        ['label' => 'T/F', 'value' => 'true_or_false_questions'],
        ['label' => 'Essay', 'value' => 'essay_questions']
    ];
    $count = function ($topic, $type) {
        if ('multiple_choice_questions' == $type) {
            return $topic['multiple_choice_questions_count'];
        }

        if ('true_or_false_questions' == $type) {
            return $topic['true_or_false_questions_count'];
        }

        if ('essay_questions' == $type) {
            return $topic['essay_questions_count'];
        }

        return '0';
    };
@endphp

<div>
    <label class="text-gray-800 font-medium text-sm">Sections</label>
    @foreach ($sections as $section)
    <div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-x-1">
            <div class="col-span-2">
                <x-form.input wire:key="name-{{ $loop->index }}" wire:model="sections.{{ $loop->index }}.name" full name="sections[{{ $loop->index }}][name]" label="Name" />
            </div>
            <div>
                <x-form.select wire:key="type-{{ $loop->index }}" wire:model="sections.{{ $loop->index }}.type" full name="sections[{{ $loop->index }}][type]" label="Type" :options="$options" />
            </div>
            <div>
                <x-form.input wire:key="count-{{ $loop->index }}" wire:model="sections.{{ $loop->index }}.count" full name="sections[{{ $loop->index }}][count]" label="Count" />
            </div>
        </div>
        <label class="block text-gray-800 font-medium text-sm mt-2">Available Questions</label>
        <fieldset>
            @foreach ($topics as $topic)
            <div class="flex items-start">
                <div class="flex h-5 items-center">
                    <input wire:key="topic-{{ $loop->parent->index }}-{{ $loop->index }}" wire:model="sections.{{ $loop->parent->index }}.topics" name="sections[{{ $loop->parent->index }}][topics][]" value="{{ $topic['id'] }}" type="checkbox" class="h-4 w-4">
                </div>
                <div class="ml-3 text-sm">
                    <label class="font-medium text-gray-700">{{ $topic['name'] }}</label>
                    <span class="text-gray-500">({{ $count($topic, $sections[$loop->parent->index]['type']) }})</span>
                </div>
            </div>
            @endforeach
        </fieldset>
    </div>
    @endforeach
    <div class="relative mt-5">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-200"></div>
        </div>
        <div class="relative flex justify-center">
            <span class="isolate inline-flex -space-x-px">
                <button wire:click="minus()" type="button" class="relative inline-flex items-center border border-gray-300 px-3 py-2 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-minus"><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                </button>
                <button wire:click="plus()" type="button" class="relative inline-flex items-center border border-gray-300 px-3 py-2 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                </button>
            </span>
        </div>
    </div>
</div>
