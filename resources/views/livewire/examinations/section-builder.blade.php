<section class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
    <div class="flex items-center justify-between mb-4">
        <h2 class="font-semibold">Sections</h2>
        <button type="button" class="px-3 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700" wire:click="addSection">+ Add Section</button>
    </div>

    @foreach($sections as $index => $section)
        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 mb-4 space-y-3 bg-gray-50 dark:bg-gray-900"
             x-data="{
                 documentPath: @js($section['document_path'] ?? ''),
                 documentName: @js($section['document_name'] ?? ''),
                 hasDocument: @js($section['has_document'] ?? false)
             }"
             @document-uploaded-{{ $index }}.window="
                 console.log('Event received:', $event.detail);
                 documentPath = $event.detail.path;
                 documentName = $event.detail.name;
                 hasDocument = true;
                 console.log('Updated state:', {documentPath, documentName, hasDocument});
             ">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-medium text-lg">Section {{ $index + 1 }}</h3>
                <button type="button" class="text-red-600 text-sm hover:text-red-800" wire:click="removeSection({{ $index }})" @if(count($sections) <= 1) disabled @endif>Remove</button>
            </div>
            
            <div class="grid md:grid-cols-2 gap-3">
                <input name="sections[{{ $index }}][title]" wire:model.blur="sections.{{ $index }}.title" placeholder="Section title (e.g., Section A)" class="px-3 py-2 border rounded-lg" required>
                <input type="number" min="1" name="sections[{{ $index }}][time_limit_minutes]" wire:model.blur="sections.{{ $index }}.time_limit_minutes" placeholder="Section time limit (minutes)" class="px-3 py-2 border rounded-lg">
            </div>

            <div class="grid md:grid-cols-3 gap-3">
                <div>
                    <label class="text-xs text-gray-600 dark:text-gray-400 mb-1 block">Source Type</label>
                    <select name="sections[{{ $index }}][source_type]" wire:model.live="sections.{{ $index }}.source_type" class="w-full px-3 py-2 border rounded-lg" required>
                        <option value="database">Database</option>
                        <option value="ai">AI Generated</option>
                        <option value="mixed">Mixed</option>
                        <option value="manual">Manual Entry</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-600 dark:text-gray-400 mb-1 block">Question Type</label>
                    <select name="sections[{{ $index }}][question_type]" wire:model.live="sections.{{ $index }}.question_type" class="w-full px-3 py-2 border rounded-lg" required>
                        <option value="multiple_choice">Multiple Choice</option>
                        <option value="true_false">True/False</option>
                        <option value="short_answer">Short Answer</option>
                        <option value="essay">Essay</option>
                        <option value="mixed">Mixed</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-600 dark:text-gray-400 mb-1 block">Randomize Questions</label>
                    <label class="inline-flex items-center px-3 py-2 border rounded-lg w-full">
                        <input type="checkbox" name="sections[{{ $index }}][is_randomized]" value="1" wire:model.live="sections.{{ $index }}.is_randomized" class="mr-2">
                        <span class="text-sm">Randomize order</span>
                    </label>
                </div>
            </div>

            @if($section['source_type'] === 'mixed')
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
                    <p class="text-sm font-medium mb-2">Mixed Source Configuration</p>
                    <div class="grid md:grid-cols-3 gap-3">
                        <div>
                            <label class="text-xs text-gray-600 dark:text-gray-400">Database Questions</label>
                            <input type="number" min="0" name="sections[{{ $index }}][database_count]" wire:model.blur="sections.{{ $index }}.database_count" placeholder="0" class="w-full px-3 py-2 border rounded-lg mt-1">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600 dark:text-gray-400">AI Generated</label>
                            <input type="number" min="0" name="sections[{{ $index }}][ai_count]" wire:model.blur="sections.{{ $index }}.ai_count" placeholder="0" class="w-full px-3 py-2 border rounded-lg mt-1">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600 dark:text-gray-400">Manual Entry</label>
                            <input type="number" min="0" name="sections[{{ $index }}][manual_count]" wire:model.blur="sections.{{ $index }}.manual_count" placeholder="0" class="w-full px-3 py-2 border rounded-lg mt-1">
                        </div>
                    </div>
                    <input type="hidden" name="sections[{{ $index }}][question_count]" value="{{ ($section['database_count'] ?? 0) + ($section['ai_count'] ?? 0) + ($section['manual_count'] ?? 0) }}">
                </div>
            @else
                <div>
                    <label class="text-xs text-gray-600 dark:text-gray-400 mb-1 block">Number of Questions</label>
                    <input type="number" min="1" name="sections[{{ $index }}][question_count]" wire:model.blur="sections.{{ $index }}.question_count" placeholder="Number of questions" class="w-full px-3 py-2 border rounded-lg" required>
                </div>
            @endif

            @if(in_array($section['source_type'] ?? '', ['ai', 'mixed'], true))
                <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-3">
                    <label class="text-sm font-medium mb-2 block">Upload Document for AI Generation</label>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">Supported: PDF, DOCX, TXT, MD</p>
                    <input type="file" 
                           wire:model="uploadedDocuments.{{ $index }}" 
                           accept=".pdf,.docx,.doc,.txt,.md" 
                           class="w-full px-3 py-2 border rounded-lg text-sm"
                           x-data="{}"
                           @change="$wire.uploadedDocuments[{{ $index }}] = $event.target.files[0]">
                    @error('uploadedDocuments.' . $index) <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    
                    <div wire:loading wire:target="uploadedDocuments.{{ $index }}" class="mt-2 text-sm text-blue-600">
                        Uploading...
                    </div>
                    
                    @if(!empty($section['document_name']))
                        <div class="mt-2 flex items-center gap-2 text-sm text-green-600 dark:text-green-400">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>{{ $section['document_name'] }} uploaded successfully</span>
                        </div>
                    @endif
                </div>
            @endif

            @if(in_array($section['source_type'] ?? '', ['database', 'mixed'], true))
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <p class="text-sm font-medium mb-3">Academic Hierarchy (for Database Questions)</p>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 block">Academic Group</label>
                            @livewire('common.searchable-multi-select', [
                                'items' => collect($hierarchyTree)->map(fn($g) => ['id' => $g['id'], 'name' => $g['name']])->values()->all(),
                                'selected' => !empty($section['academic_group_id']) ? [(string) $section['academic_group_id']] : [],
                                'name' => "sections[{$index}][academic_group_id]",
                                'multiple' => false,
                                'placeholder' => 'Select group',
                            ], key("section-{$index}-group-".($section['academic_group_id'] ?? 'none').'-'.md5(json_encode($section))))
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 block">Academic Level</label>
                            @livewire('common.searchable-multi-select', [
                                'items' => $this->levelItems($index),
                                'selected' => !empty($section['academic_level_id']) ? [(string) $section['academic_level_id']] : [],
                                'name' => "sections[{$index}][academic_level_id]",
                                'multiple' => false,
                                'placeholder' => 'Select level',
                            ], key("section-{$index}-level-".($section['academic_level_id'] ?? 'none').'-'.md5(json_encode($section))))
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 block">Subject</label>
                            @livewire('common.searchable-multi-select', [
                                'items' => $this->subjectItems($index),
                                'selected' => !empty($section['academic_subject_id']) ? [(string) $section['academic_subject_id']] : [],
                                'name' => "sections[{$index}][academic_subject_id]",
                                'multiple' => false,
                                'placeholder' => 'Select subject',
                            ], key("section-{$index}-subject-".($section['academic_subject_id'] ?? 'none').'-'.md5(json_encode($section))))
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 block">Topics (optional)</label>
                            @livewire('common.searchable-multi-select', [
                                'items' => $this->topicItems($index),
                                'selected' => array_map('strval', $section['topic_ids'] ?? []),
                                'name' => "sections[{$index}][topic_ids]",
                                'multiple' => true,
                                'placeholder' => 'Select topics',
                            ], key("section-{$index}-topics-".md5(json_encode($section['topic_ids'] ?? [])).'-'.md5(json_encode($section))))
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 block">Subtopics (optional)</label>
                            @livewire('common.searchable-multi-select', [
                                'items' => $this->subtopicItems($index),
                                'selected' => array_map('strval', $section['subtopic_ids'] ?? []),
                                'name' => "sections[{$index}][subtopic_ids]",
                                'multiple' => true,
                                'placeholder' => 'Select subtopics',
                            ], key("section-{$index}-subtopics-".md5(json_encode($section['subtopic_ids'] ?? [])).'-'.md5(json_encode($section))))
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid md:grid-cols-2 gap-3">
                <textarea name="sections[{{ $index }}][description]" wire:model.blur="sections.{{ $index }}.description" placeholder="Section description (optional)" class="w-full px-3 py-2 border rounded-lg" rows="2"></textarea>
                <textarea name="sections[{{ $index }}][instructions]" wire:model.blur="sections.{{ $index }}.instructions" placeholder="Section instructions (shown to students)" class="w-full px-3 py-2 border rounded-lg" rows="2"></textarea>
            </div>
            
            <!-- Alpine.js managed hidden inputs for document data -->
            <input type="hidden" name="sections[{{ $index }}][document_path]" x-bind:value="hasDocument ? documentPath : ''" x-init="console.log('Input {{ $index }} initialized:', {hasDocument, documentPath})">
            <input type="hidden" name="sections[{{ $index }}][document_name]" x-bind:value="hasDocument ? documentName : ''">
            <input type="hidden" name="sections[{{ $index }}][has_document]" x-bind:value="hasDocument ? '1' : '0'">
            
            <!-- Debug display -->
            <div x-show="hasDocument" class="text-xs text-gray-500 mt-2">
                Debug: Path = <span x-text="documentPath"></span>
            </div>
        </div>
    @endforeach
</section>

