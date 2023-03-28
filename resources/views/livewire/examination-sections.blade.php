@php
    $options = [
        'multiple_choice_questions' => 'Multiple Choice',
        'true_or_false_questions' => 'True/False',
        'essay_questions' => 'Essay'
    ];
    $count = function ($topic, $type) {
        if ('true_or_false_questions' == $type) {
            return $topic['true_or_false_questions_count'];
        }

        if ('essay_questions' == $type) {
            return $topic['essay_questions_count'];
        }

        return $topic['multiple_choice_questions_count'];;
    };
@endphp

<div>
    <label class="block text-gray-800 font-medium text-sm">Sections</label>
    @foreach ($sections as $section)
    <div class="mt-3">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-x-3">
            <div class="col-span-2">
                <x-form.input wire:key="name-{{ $loop->index }}" wire:model="sections.{{ $loop->index }}.name" name="sections[{{ $loop->index }}][name]" type="text" label="Name" />
            </div>
            <div>
                <x-form.select wire:key="type-{{ $loop->index }}" wire:model="sections.{{ $loop->index }}.type" name="sections[{{ $loop->index }}][type]" type="text" label="Type" :options="$options" />
            </div>
            <div>
                <x-form.input wire:key="count-{{ $loop->index }}" wire:model="sections.{{ $loop->index }}.count" name="sections[{{ $loop->index }}][count]" type="text" label="Count" />
            </div>
        </div>
        <label class="block text-gray-800 font-medium text-sm mt-3">Available Questions</label>

        <fieldset>
            <div class="overflow-hidden rounded-lg border border-gray-300 bg-white mt-3">
                <ul role="list" class="divide-y divide-gray-300">
                    @foreach ($topics as $topic)
                    <li class="p-3">
                        <div class="flex items-center justify-between text-sm">
                            <div class="relative flex items-start">
                                <div class="flex h-6 items-center">
                                    <input wire:key="topic-{{ $loop->parent->index }}-{{ $loop->index }}" wire:model="sections.{{ $loop->parent->index }}.topics" name="sections[{{ $loop->parent->index }}][topics][]" value="{{ $topic['id'] }}" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                </div>
                                <div class="ml-3  leading-6">
                                    <label class="font-medium text-gray-700">{{ $topic['name'] }}</label>
                                </div>
                            </div>
                            <div>
                                <span class="text-gray-500">{{ $count($topic, $sections[$loop->parent->index]['type']) }} Available</span>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </fieldset>
    </div>
    @endforeach
    <div class="relative mt-5">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-200"></div>
        </div>
        <div class="relative flex justify-center">
            <span class="isolate inline-flex -space-x-px">
                <button wire:click="minus()" type="button" class="relative inline-flex items-center border border-gray-300 px-3 py-2 bg-white hover:bg-gray-50 rounded-l-lg">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-minus"><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                </button>
                <button wire:click="plus()" type="button" class="relative inline-flex items-center border border-gray-300 px-3 py-2 bg-white hover:bg-gray-50 rounded-r-lg">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                </button>
            </span>
        </div>
    </div>
</div>
