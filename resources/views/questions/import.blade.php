<x-layouts.app :show-title-area="false" page-name="Import Questions" title="Import Questions" action-link-text="" :action_link="''">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Dashboard' => route('dashboard'),
            'Academic Groups' => route('academic-groups.index'),
            isset($academicSubject) && $academicSubject ? $academicSubject->academicLevel->academicGroup->name : '' => isset($academicSubject) && $academicSubject ? route('academic-groups.show', ['academic_group' => $academicSubject->academicLevel->academicGroup]) : '',
            isset($academicSubject) && $academicSubject ? $academicSubject->academicLevel->name : '' => isset($academicSubject) && $academicSubject ? route('academic-levels.show', ['academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]) : '',
            isset($academicSubject) && $academicSubject ? $academicSubject->name : '' => isset($academicSubject) && $academicSubject ? route('academic-subjects.show', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]) : '',
            isset($academicTopic) && $academicTopic ? $academicTopic->name : 'Import Questions' => isset($academicTopic) && $academicTopic ? route('academic-topics.show', ['academic_topic' => $academicTopic, 'academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]) : null,
            'Import Questions' => null,
        ]" />
    </x-slot>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-8xl mx-auto">

            {{-- ── Flash: import success ── --}}
            @if(session('import_success'))
                <div class="mb-6 rounded-lg border border-green-200 bg-green-50 dark:border-green-700 dark:bg-green-900/30 p-4">
                    <div class="flex items-start gap-3">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-green-500 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-sm text-green-800 dark:text-green-200">
                            {!! session('import_success') !!}
                        </div>
                    </div>
                </div>
            @endif

            {{-- ── Flash: top-level import error ── --}}
            @if(session('import_error'))
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 dark:border-red-700 dark:bg-red-900/30 p-4">
                    <div class="flex items-start gap-3">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-sm text-red-800 dark:text-red-200">
                            {{ session('import_error') }}
                        </div>
                    </div>
                </div>
            @endif

            {{-- ── Validation errors (e.g. wrong file type/size) ── --}}
            @if($errors->has('questions_file'))
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 dark:border-red-700 dark:bg-red-900/30 p-4">
                    <div class="flex items-start gap-3">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-sm text-red-800 dark:text-red-200">
                            {{ $errors->first('questions_file') }}
                        </div>
                    </div>
                </div>
            @endif

            {{-- ── Per-row import errors ── --}}
            @if(session('import_row_errors') && count(session('import_row_errors')) > 0)
                <div class="mb-6 rounded-lg border border-orange-200 bg-orange-50 dark:border-orange-700 dark:bg-orange-900/30 p-4">
                    <div class="flex items-start gap-3">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-orange-800 dark:text-orange-200 mb-2">
                                {{ count(session('import_row_errors')) }} question(s) could not be imported:
                            </p>
                            <ul class="space-y-1">
                                @foreach(session('import_row_errors') as $rowError)
                                    <li class="text-sm text-orange-700 dark:text-orange-300">
                                        <span class="font-medium">Row {{ $rowError['row'] ?? '?' }}:</span>
                                        {{ $rowError['message'] ?? 'Unknown error' }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
                        Import Questions
                        @if(isset($academicTopic) && $academicTopic)
                            for "{{ $academicTopic->name }}"
                        @elseif(isset($academicSubject) && $academicSubject)
                            for "{{ $academicSubject->name }}"
                        @else
                            for Selected Topic
                        @endif
                    </h2>
                </div>

                <div class="p-6">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Instructions</h3>
                        <ul class="list-disc pl-5 space-y-1 text-sm text-gray-600 dark:text-gray-300">
                            <li>Excel (.xlsx, .xls) maps your data column-by-column — use it for precise, bulk, or subject-level imports.</li>
                            <li>Word (.docx, .doc) and PDF use AI to read the document, classify each question as multiple choice, true/false, or essay, and detect the correct answer — best for topic-level imports of existing question banks or past papers.</li>
                            @if(!isset($academicTopic) || !$academicTopic)
                                <li>For subject-level import, the <strong>academic_topic_id</strong> column is required in the Excel template to specify which topic each question belongs to.</li>
                                <li class="text-amber-700 dark:text-amber-400 font-medium">Word and PDF import is only available at topic level — open a specific topic to use it.</li>
                            @else
                                <li>For Word/PDF, AI-determined answers are flagged for review on the preview screen whenever the document had no clear answer marker (e.g. bold text or an answer key) — double-check those before confirming.</li>
                            @endif
                            <li>Maximum file size: 10MB</li>
                        </ul>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Download Template</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                                Use this template to format your questions correctly for Excel import.
                            </p>

                            @if(isset($academicTopic) && $academicTopic && isset($academicSubject) && $academicSubject)
                                <a href="{{ route('questions.download.template', [
                                    'academic_topic' => $academicTopic,
                                    'academic_subject' => $academicSubject,
                                    'academic_level' => $academicSubject->academicLevel,
                                    'academic_group' => $academicSubject->academicLevel->academicGroup
                                ]) }}"
                                   class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    Download Template
                                </a>
                            @elseif(isset($academicSubject) && $academicSubject)
                                <a href="{{ route('questions.subject.download.template', [
                                    'academic_subject' => $academicSubject,
                                    'academic_level' => $academicSubject->academicLevel,
                                    'academic_group' => $academicSubject->academicLevel->academicGroup
                                ]) }}"
                                   class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    Download Template
                                </a>
                            @else
                                <span class="text-sm text-red-600">Unable to load template - no subject specified</span>
                            @endif
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Upload File</h3>
                            <form method="POST"
                                  id="import-upload-form"
                                  action="{{ isset($academicTopic) && $academicTopic && isset($academicSubject) && $academicSubject ?
                                    route('questions.preview', [
                                        'academic_topic' => $academicTopic,
                                        'academic_subject' => $academicSubject,
                                        'academic_level' => $academicSubject->academicLevel,
                                        'academic_group' => $academicSubject->academicLevel->academicGroup
                                    ]) :
                                    (isset($academicSubject) && $academicSubject ?
                                        route('questions.subject.preview', [
                                            'academic_subject' => $academicSubject,
                                            'academic_level' => $academicSubject->academicLevel,
                                            'academic_group' => $academicSubject->academicLevel->academicGroup
                                        ]) : '#')
                                  }}"
                                  enctype="multipart/form-data">
                                @csrf

                                <div class="mb-4">
                                    <label for="questions_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Select Question File
                                    </label>
                                    <input type="file" name="questions_file" id="questions_file"
                                           data-topic-available="{{ isset($academicTopic) && $academicTopic ? '1' : '0' }}"
                                           class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                                           accept=".xlsx,.xls,.docx,.doc,.pdf" required>

                                    <p id="selected-file-info" class="hidden mt-2 text-sm text-gray-600 dark:text-gray-300"></p>
                                    <p id="ai-document-notice" class="hidden mt-2 text-sm text-blue-700 dark:text-blue-300">
                                        This file will be processed with AI. You'll get a chance to review every extracted question and answer before anything is saved.
                                    </p>
                                    <p id="topic-required-warning" class="hidden mt-2 text-sm text-red-600 dark:text-red-400">
                                        Word and PDF import requires a specific topic. Please go to a topic page and import from there, or choose an Excel (.xlsx/.xls) file for subject-level import.
                                    </p>

                                    @error('questions_file')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <button type="submit"
                                        id="preview-submit-button"
                                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-50 disabled:cursor-not-allowed transition ease-in-out duration-150">
                                    <svg id="preview-submit-icon" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span id="preview-submit-label">Preview Questions</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Supported Formats</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="w-6 h-6 text-blue-500 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span class="font-medium text-blue-800 dark:text-blue-200">Excel</span>
                                    </div>
                                    <span class="text-[10px] font-semibold uppercase tracking-wide text-blue-500 dark:text-blue-300">Direct mapping</span>
                                </div>
                                <p class="text-sm text-blue-600 dark:text-blue-300 mt-1">.xlsx, .xls files</p>
                                <p class="text-xs text-blue-500 dark:text-blue-400 mt-1">Works at subject or topic level. Best for bulk/precise data.</p>
                            </div>
                            <div class="bg-green-50 dark:bg-green-900/30 p-4 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="w-6 h-6 text-green-500 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span class="font-medium text-green-800 dark:text-green-200">Word</span>
                                    </div>
                                    <span class="text-[10px] font-semibold uppercase tracking-wide text-green-500 dark:text-green-300">AI-powered</span>
                                </div>
                                <p class="text-sm text-green-600 dark:text-green-300 mt-1">.docx, .doc files</p>
                                <p class="text-xs text-green-500 dark:text-green-400 mt-1">Topic level only. Detects bold/underlined answers automatically.</p>
                            </div>
                            <div class="bg-purple-50 dark:bg-purple-900/30 p-4 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="w-6 h-6 text-purple-500 dark:text-purple-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span class="font-medium text-purple-800 dark:text-purple-200">PDF</span>
                                    </div>
                                    <span class="text-[10px] font-semibold uppercase tracking-wide text-purple-500 dark:text-purple-300">AI-powered</span>
                                </div>
                                <p class="text-sm text-purple-600 dark:text-purple-300 mt-1">.pdf files</p>
                                <p class="text-xs text-purple-500 dark:text-purple-400 mt-1">Topic level only. Falls back to an answer key or AI judgment if no formatting is present — always flagged for review.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const fileInput = document.getElementById('questions_file');
            const fileInfo = document.getElementById('selected-file-info');
            const aiNotice = document.getElementById('ai-document-notice');
            const topicWarning = document.getElementById('topic-required-warning');
            const submitButton = document.getElementById('preview-submit-button');
            const submitLabel = document.getElementById('preview-submit-label');
            const submitIcon = document.getElementById('preview-submit-icon');
            const form = document.getElementById('import-upload-form');

            const AI_EXTENSIONS = ['docx', 'doc', 'pdf'];
            const topicAvailable = fileInput?.dataset.topicAvailable === '1';

            function formatBytes(bytes) {
                if (bytes < 1024) return bytes + ' B';
                if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
                return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
            }

            function refreshFileState() {
                const file = fileInput?.files?.[0];

                fileInfo.classList.add('hidden');
                aiNotice.classList.add('hidden');
                topicWarning.classList.add('hidden');
                submitButton.disabled = false;

                if (!file) {
                    return;
                }

                const extension = file.name.split('.').pop().toLowerCase();

                fileInfo.textContent = `Selected: ${file.name} (${formatBytes(file.size)})`;
                fileInfo.classList.remove('hidden');

                if (AI_EXTENSIONS.includes(extension)) {
                    if (!topicAvailable) {
                        topicWarning.classList.remove('hidden');
                        submitButton.disabled = true;
                    } else {
                        aiNotice.classList.remove('hidden');
                    }
                }
            }

            fileInput?.addEventListener('change', refreshFileState);

            form?.addEventListener('submit', function () {
                if (submitButton.disabled) {
                    return;
                }
                submitButton.disabled = true;
                submitButton.classList.add('opacity-75', 'cursor-not-allowed');
                submitLabel.textContent = 'Uploading…';
                submitIcon.classList.add('animate-spin');
            });
        });
    </script>
</x-layouts.app>
