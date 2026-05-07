<x-layouts.app>
    <x-examinations-hub.navigation active="create" />
    
    @php
        $seed = $formData ?? old();
        $seedSections = $seed['sections'] ?? [['title'=>'Section A','description'=>'','instructions'=>'','time_limit_minutes'=>'','source_type'=>'database','question_type'=>'multiple_choice','question_count'=>10,'database_count'=>0,'ai_count'=>0,'manual_count'=>0,'is_randomized'=>false,'academic_group_id'=>'','academic_level_id'=>'','academic_subject_id'=>'','topic_ids'=>[],'subtopic_ids'=>[],'has_document'=>false]];
    @endphp
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ isset($editingExam) && $editingExam ? 'Edit Examination' : 'Create Examination' }}</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Configure your examination with sections, question sources, and participant settings</p>
        </div>
        
        <form method="POST" action="{{ route('examinations-hub.create.preview') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <input type="hidden" name="exam_id" value="{{ $seed['exam_id'] ?? '' }}">

            <section class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 shadow-sm">
                <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Exam Configuration</h2>
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Exam Title</label>
                        <input name="title" value="{{ $seed['title'] ?? '' }}" placeholder="e.g., Final Mathematics Examination 2024" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                        <select name="status" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white" required>
                            <option value="draft" @selected(($seed['status'] ?? 'draft')==='draft')>Draft</option>
                            <option value="published" @selected(($seed['status'] ?? '')==='published')>Published</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Exam Mode</label>
                        <select name="hardened_mode" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                            <option value="0" @selected(($seed['hardened_mode'] ?? '0')==='0')>Normal (Preview Questions)</option>
                            <option value="1" @selected(($seed['hardened_mode'] ?? '')==='1')>Hardened (No Preview)</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Hardened mode prevents viewing questions before exam creation</p>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                        <textarea name="description" placeholder="Brief description of the examination" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white" rows="2">{{ $seed['description'] ?? '' }}</textarea>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">General Instructions</label>
                        <textarea name="instructions" placeholder="Instructions and rules for the entire examination" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white" rows="3">{{ $seed['instructions'] ?? '' }}</textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Total Duration (minutes)</label>
                        <input type="number" min="1" name="duration_in_minutes" value="{{ $seed['duration_in_minutes'] ?? '' }}" placeholder="e.g., 120" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    
                    <div></div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date & Time</label>
                        <input type="datetime-local" name="starts_at" value="{{ $seed['starts_at'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date & Time</label>
                        <input type="datetime-local" name="ends_at" value="{{ $seed['ends_at'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white" required>
                    </div>
                </div>
            </section>

            <section class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 shadow-sm">
                <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Participant Access Control</h2>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Participant Mode</label>
                        <select name="participant_mode" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white" required>
                            <option value="general" @selected(($seed['participant_mode'] ?? 'general')==='general')>General (Anyone with code)</option>
                            <option value="configured" @selected(($seed['participant_mode'] ?? '')==='configured')>Configured (Pre-registered only)</option>
                            <option value="both" @selected(($seed['participant_mode'] ?? '')==='both')>Both</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Configured Match Rule</label>
                        <select name="configured_match_mode" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white" required>
                            <option value="any" @selected(($seed['configured_match_mode'] ?? 'any')==='any')>Match email OR code</option>
                            <option value="both" @selected(($seed['configured_match_mode'] ?? '')==='both')>Match email AND code</option>
                        </select>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Required Participant Fields</label>
                        @php $oldFields = $seed['participant_required_fields'] ?? ['name','email']; @endphp
                        @livewire('common.searchable-multi-select', [
                            'items' => [
                                ['id' => 'name', 'name' => 'Name'],
                                ['id' => 'email', 'name' => 'Email'],
                                ['id' => 'code', 'name' => 'Unique Code'],
                            ],
                            'selected' => $oldFields,
                            'name' => 'participant_required_fields',
                            'multiple' => true,
                            'placeholder' => 'Select required fields',
                        ], key('participant-required-fields-'.md5(json_encode($oldFields))))
                    </div>
                </div>
            </section>

            @livewire('examinations.section-builder', [
                'sections' => $seedSections,
                'hierarchyTree' => $hierarchyTree,
            ], key('exam-section-builder-'.md5(json_encode($seedSections))))

            @if($errors->any())
                <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                    <p class="text-red-700 dark:text-red-400 font-medium">Please fix the following errors:</p>
                    <ul class="list-disc list-inside text-red-600 dark:text-red-400 text-sm mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex justify-between items-center">
                <a href="{{ route('examinations-hub.dashboard') }}" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">Cancel</a>
                <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium shadow-sm">{{ isset($editingExam) && $editingExam ? 'Preview Changes' : 'Preview Examination' }}</button>
            </div>
        </form>
    </div>
</x-layouts.app>
