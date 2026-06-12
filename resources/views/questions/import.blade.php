<x-layouts.app title="Import Questions" action-link-text="" :action_link="''">
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
        <div class="max-w-3xl mx-auto">

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
                            <li>Download the template below and fill in your questions</li>
                            <li>Supported formats: Excel (xlsx, xls), Word (docx, doc), PDF</li>
                            @if(!isset($academicTopic) || !$academicTopic)
                                <li>For subject-level import, the <strong>academic_topic_id</strong> column is required to specify which topic each question belongs to</li>
                            @endif
                            <li>Maximum file size: 10MB</li>
                        </ul>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Download Template</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                                Use this template to format your questions correctly.
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
                            <form method="POST" action="{{ isset($academicTopic) && $academicTopic && isset($academicSubject) && $academicSubject ? 
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
                            }}" enctype="multipart/form-data">
                                @csrf
                                
                                <div class="mb-4">
                                    <label for="questions_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Select Question File
                                    </label>
                                    <input type="file" name="questions_file" id="questions_file" 
                                           class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" 
                                           accept=".xlsx,.xls,.docx,.doc,.pdf" required>
                                    @error('questions_file')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Preview Questions
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Supported Formats</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-blue-500 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span class="font-medium text-blue-800 dark:text-blue-200">Excel</span>
                                </div>
                                <p class="text-sm text-blue-600 dark:text-blue-300 mt-1">.xlsx, .xls files</p>
                            </div>
                            <div class="bg-green-50 dark:bg-green-900/30 p-4 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-green-500 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span class="font-medium text-green-800 dark:text-green-200">Word</span>
                                </div>
                                <p class="text-sm text-green-600 dark:text-green-300 mt-1">.docx, .doc files</p>
                            </div>
                            <div class="bg-purple-50 dark:bg-purple-900/30 p-4 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-purple-500 dark:text-purple-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span class="font-medium text-purple-800 dark:text-purple-200">PDF</span>
                                </div>
                                <p class="text-sm text-purple-600 dark:text-purple-300 mt-1">.pdf files</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>