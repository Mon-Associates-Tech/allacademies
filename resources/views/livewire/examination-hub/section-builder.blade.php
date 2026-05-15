<section class="bg-white dark:bg-slate-900 overflow-hidden"
         style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
        <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Sections</h2>
        <button type="button"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white transition-all"
                style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);"
                wire:click="addSection">
            <x-heroicon-o-plus class="w-4 h-4" />
            Add Section
        </button>
    </div>

    <div class="p-5 space-y-5">
        @foreach($sections as $index => $section)
            <div class="p-4 border hover:bg-amber-50/40 dark:hover:bg-slate-800/40 transition-colors"
                 style="border-radius: 2px; border-color: rgba(0,0,0,0.06);"
                 x-data="{
                     documentPath: @js($section['document_path'] ?? ''),
                     documentName: @js($section['document_name'] ?? ''),
                     hasDocument: @js($section['has_document'] ?? false)
                 }"
                 @document-uploaded-{{ $index }}.window="
                     documentPath = $event.detail.path;
                     documentName = $event.detail.name;
                     hasDocument = true;
                 ">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-slate-900 dark:text-white">Section {{ $index + 1 }}</h3>
                    <button type="button"
                            class="text-xs font-semibold text-red-700 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 transition-colors"
                            wire:click="removeSection({{ $index }})"
                            @if(count($sections) <= 1) disabled @endif>
                        Remove
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Title -->
                    <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                            Section Title <span class="text-red-500">*</span>
                        </label>
                        <input name="sections[{{ $index }}][title]"
                               wire:model.blur="sections.{{ $index }}.title"
                               placeholder="e.g., Section A"
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                               style="border-radius: 2px;" required>
                    </div>

                    <!-- Time Limit -->
                    <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                            Time Limit (minutes)
                        </label>
                        <input type="number" min="1"
                               name="sections[{{ $index }}][time_limit_minutes]"
                               wire:model.blur="sections.{{ $index }}.time_limit_minutes"
                               placeholder="e.g., 30"
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                               style="border-radius: 2px;">
                    </div>
                </div>

                <!-- Source Configuration Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <!-- Source Type -->
                    <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                            Source Type <span class="text-red-500">*</span>
                        </label>
                        <select name="sections[{{ $index }}][source_type]"
                                wire:model.live="sections.{{ $index }}.source_type"
                                class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                style="border-radius: 2px;" required>
                            <option value="database">Database</option>
                            <option value="ai">AI Generated</option>
                            <option value="mixed">Mixed</option>
                            <option value="manual">Manual Entry</option>
                        </select>
                    </div>

                    <!-- Question Type -->
                    <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                            Question Type <span class="text-red-500">*</span>
                        </label>
                        <select name="sections[{{ $index }}][question_type]"
                                wire:model.live="sections.{{ $index }}.question_type"
                                class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                style="border-radius: 2px;" required>
                            <option value="multiple_choice">Multiple Choice</option>
                            <option value="true_false">True/False</option>
                            <option value="short_answer">Short Answer</option>
                            <option value="essay">Essay</option>
                            <option value="mixed">Mixed</option>
                        </select>
                    </div>

                    <!-- Randomize -->
                    <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                            Randomize Questions
                        </label>
                        <label class="inline-flex items-center px-4 py-2.5 w-full text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 transition-all"
                               style="border-radius: 2px;">
                            <input type="checkbox"
                                   name="sections[{{ $index }}][is_randomized]"
                                   value="1"
                                   wire:model.live="sections.{{ $index }}.is_randomized"
                                   class="mr-3 h-4 w-4 text-amber-600 border-slate-300 dark:border-slate-600 rounded focus:ring-amber-500">
                            <span>Randomize order</span>
                        </label>
                    </div>
                </div>

                <!-- Mixed Source Configuration -->
                @if($section['source_type'] === 'mixed')
                    <div class="mt-5 p-4 border"
                         style="border-radius: 2px; color:#1d4ed8;background:#eff6ff;border-color:#bfdbfe;">
                        <p class="text-sm font-semibold text-slate-900 dark:text-white mb-3">Mixed Source Configuration</p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                                    Database Questions
                                </label>
                                <input type="number" min="0"
                                       name="sections[{{ $index }}][database_count]"
                                       wire:model.blur="sections.{{ $index }}.database_count"
                                       placeholder="0"
                                       class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                       style="border-radius: 2px;">
                            </div>
                            <div>
                                <label class="block text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                                    AI Generated
                                </label>
                                <input type="number" min="0"
                                       name="sections[{{ $index }}][ai_count]"
                                       wire:model.blur="sections.{{ $index }}.ai_count"
                                       placeholder="0"
                                       class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                       style="border-radius: 2px;">
                            </div>
                            <div>
                                <label class="block text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                                    Manual Entry
                                </label>
                                <input type="number" min="0"
                                       name="sections[{{ $index }}][manual_count]"
                                       wire:model.blur="sections.{{ $index }}.manual_count"
                                       placeholder="0"
                                       class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                       style="border-radius: 2px;">
                            </div>
                        </div>
                        <input type="hidden" name="sections[{{ $index }}][question_count]" value="{{ ($section['database_count'] ?? 0) + ($section['ai_count'] ?? 0) + ($section['manual_count'] ?? 0) }}">
                    </div>
                @else
                    <!-- Question Count -->
                    <div class="mt-5">
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                            Number of Questions <span class="text-red-500">*</span>
                        </label>
                        <input type="number" min="1"
                               name="sections[{{ $index }}][question_count]"
                               wire:model.blur="sections.{{ $index }}.question_count"
                               placeholder="e.g., 10"
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                               style="border-radius: 2px;" required>
                    </div>
                @endif

                <!-- AI Document Upload -->
                @if(in_array($section['source_type'] ?? '', ['ai', 'mixed'], true))
                    <div class="mt-5 p-4 border"
                         style="border-radius: 2px; color:#7c2d12;background:#fffbeb;border-color:#fde68a;">
                        <label class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">Upload Document for AI Generation</label>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">Supported: PDF, DOCX, TXT, MD</p>
                        <input type="file"
                               wire:model="uploadedDocuments.{{ $index }}"
                               accept=".pdf,.docx,.doc,.txt,.md"
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                               style="border-radius: 2px;"
                               x-data="{}"
                               @change="$wire.uploadedDocuments[{{ $index }}] = $event.target.files[0]">

                        @error('uploadedDocuments.' . $index)
                            <p class="mt-2 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror

                        <div wire:loading wire:target="uploadedDocuments.{{ $index }}" class="mt-2 text-sm text-amber-700 dark:text-amber-400">
                            Uploading...
                        </div>

                        @if(!empty($section['document_name']))
                            <div class="mt-3 flex items-center gap-2 text-sm text-emerald-700 dark:text-emerald-400">
                                <x-heroicon-o-check-circle class="w-4 h-4" />
                                <span>{{ $section['document_name'] }} uploaded successfully</span>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Topic Filters -->
                @if(in_array($section['source_type'] ?? '', ['database', 'mixed'], true))
                    <div class="mt-5 p-4 border border-emerald-300 rounded-sm bg-emerald-50 text-emerald-800 dark:bg-emerald-900/30 dark:border-emerald-700/50 dark:text-emerald-200">
                        <p class="text-sm font-semibold text-slate-900 dark:text-white mb-3">Topic & Subtopic Filters (optional)</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Narrow down questions by specific topics and subtopics</p>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Topics (optional)</label>
                                @livewire('common.searchable-multi-select', [
                                    'items' => $this->topicItems($index),
                                    'selected' => array_map('strval', $section['topic_ids'] ?? []),
                                    'name' => "sections[{$index}][topic_ids]",
                                    'multiple' => true,
                                    'placeholder' => 'Select topics',
                                ], key("section-{$index}-topics-".md5(json_encode($section['topic_ids'] ?? [])).'-'.md5(json_encode($section))))
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Subtopics (optional)</label>
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
                <!-- Descriptions -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                            Section Description
                        </label>
                        <textarea name="sections[{{ $index }}][description]"
                                  wire:model.blur="sections.{{ $index }}.description"
                                  placeholder="Optional description"
                                  class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                  style="border-radius: 2px;" rows="2"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                            Section Instructions
                        </label>
                        <textarea name="sections[{{ $index }}][instructions]"
                                  wire:model.blur="sections.{{ $index }}.instructions"
                                  placeholder="Instructions shown to students"
                                  class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                  style="border-radius: 2px;" rows="2"></textarea>
                    </div>
                </div>

                <!-- Hidden Document Inputs -->
                <input type="hidden" name="sections[{{ $index }}][document_path]" x-bind:value="hasDocument ? documentPath : ''">
                <input type="hidden" name="sections[{{ $index }}][document_name]" x-bind:value="hasDocument ? documentName : ''">
                <input type="hidden" name="sections[{{ $index }}][has_document]" x-bind:value="hasDocument ? '1' : '0'">
            </div>
        @endforeach
    </div>
</section>
