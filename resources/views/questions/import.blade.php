<x-layouts.app title="Import Questions" action-link-text="" :action_link="''">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Dashboard' => route('dashboard'),
            'Academic Groups' => route('academic-groups.index'),
            $academicTopic->academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
            $academicTopic->academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
            $academicTopic->academicSubject->name => route('academic-subjects.show', ['academic_subject' => $academicTopic->academicSubject, 'academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
            $academicTopic->name => route('academic-topics.show', ['academic_topic' => $academicTopic, 'academic_subject' => $academicTopic->academicSubject, 'academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
            'Import Questions' => null,
        ]" />
    </x-slot>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
                        Import Questions to "{{ $academicTopic->name }}"
                        @if($academicSubtopic)
                            <span class="text-sm font-normal text-gray-600 dark:text-gray-400"> / "{{ $academicSubtopic->name }}"</span>
                        @endif
                    </h2>
                </div>

                <form action="{{ route('questions.preview', [
                    'academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup->id,
                    'academic_level' => $academicTopic->academicSubject->academicLevel->id,
                    'academic_subject' => $academicTopic->academicSubject->id,
                    'academic_topic' => $academicTopic->id
                ]) }}" method="POST" enctype="multipart/form-data" class="p-6">
                    @csrf

                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Supported File Formats</h3>
                        <ul class="list-disc pl-5 space-y-1 text-gray-700 dark:text-gray-300">
                            <li><strong>Excel:</strong> .xlsx, .xls files with structured question data</li>
                            <li><strong>Word:</strong> .docx, .doc files (AI will extract and format questions)</li>
                            <li><strong>PDF:</strong> .pdf files (AI will extract and format questions)</li>
                        </ul>
                    </div>

                    <div class="mb-6">
                        <label for="questions_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Select File to Import
                        </label>
                        
                        <div id="drop-area" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-md transition-colors duration-200">
                            <div class="space-y-1 text-center">
                                <svg id="upload-icon" class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                
                                <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                    <label for="questions_file" class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none">
                                        <span>Upload a file</span>
                                        <input id="questions_file" name="questions_file" type="file" class="sr-only" accept=".xlsx,.xls,.docx,.doc,.pdf" required>
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Excel, Word, or PDF up to 10MB
                                </p>
                                
                                <div id="file-info" class="hidden mt-2 p-2 bg-blue-50 dark:bg-blue-900/20 rounded-md">
                                    <p class="text-sm text-blue-700 dark:text-blue-300">
                                        <span id="file-name"></span> (<span id="file-size"></span>)
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        @error('questions_file')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Excel Format Guidelines</h3>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                            <div class="flex justify-between items-center mb-2">
                                <p class="text-sm text-gray-700 dark:text-gray-300">For Excel files, use these column headers:</p>
                                <a href="{{ route('questions.template.download', [
                                    'academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup->id,
                                    'academic_level' => $academicTopic->academicSubject->academicLevel->id,
                                    'academic_subject' => $academicTopic->academicSubject->id,
                                    'academic_topic' => $academicTopic->id
                                ]) }}" 
                                   class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-300 dark:hover:bg-indigo-800/50">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    Download Template
                                </a>
                            </div>
                            <ul class="list-disc pl-5 space-y-1 text-sm text-gray-700 dark:text-gray-300">
                                <li><strong>Required:</strong> question, type (multiple_choice, true_false, essay)</li>
                                <li><strong>For Multiple Choice:</strong> option_a, option_b, option_c, option_d, option_e, answer (A, B, C, D, E)</li>
                                <li><strong>For True/False:</strong> answer (true/false, yes/no, 1/0)</li>
                                <li><strong>For Essay:</strong> answer (sample answer)</li>
                                <li><strong>Optional:</strong> difficulty_level (easy, medium, hard), score</li>
                            </ul>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-4">
                        <a href="{{ route('academic-topics.show', [
                            'academic_topic' => $academicTopic,
                            'academic_subject' => $academicTopic->academicSubject,
                            'academic_level' => $academicTopic->academicSubject->academicLevel,
                            'academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup
                        ]) }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            Cancel
                        </a>
                        
                        <button type="submit" id="preview-btn" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" disabled>
                            Preview Questions
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if(session('import_errors'))
        <div class="container mx-auto px-4 mt-6">
            <div class="max-w-3xl mx-auto">
                <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 001.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Import Errors</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5">
                                    @foreach(session('import_errors') as $error)
                                        <li>Row {{ $error['row'] }}: {{ $error['message'] }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('questions_file');
        const dropArea = document.getElementById('drop-area');
        const fileInfo = document.getElementById('file-info');
        const fileName = document.getElementById('file-name');
        const fileSize = document.getElementById('file-size');
        const previewBtn = document.getElementById('preview-btn');
        const uploadIcon = document.getElementById('upload-icon');
        
        // Handle file selection via input
        fileInput.addEventListener('change', function(e) {
            if (this.files.length > 0) {
                handleFile(this.files[0]);
            }
        });
        
        // Prevent default drag behaviors
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });
        
        // Highlight drop area when item is dragged over it
        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, unhighlight, false);
        });
        
        // Handle dropped files
        dropArea.addEventListener('drop', handleDrop, false);
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        function highlight() {
            dropArea.classList.add('border-blue-400', 'bg-blue-50', 'dark:bg-blue-900/20');
        }
        
        function unhighlight() {
            dropArea.classList.remove('border-blue-400', 'bg-blue-50', 'dark:bg-blue-900/20');
        }
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length > 0) {
                handleFile(files[0]);
            }
        }
        
        function handleFile(file) {
            // Validate file type
            const validTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 
                               'application/vnd.ms-excel', 
                               'application/msword', 
                               'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                               'application/pdf'];
            
            if (!validTypes.includes(file.type) && !file.name.toLowerCase().match(/\.(xlsx|xls|docx|doc|pdf)$/)) {
                alert('Invalid file type. Please select an Excel, Word, or PDF file.');
                return;
            }
            
            // Update UI to show file info
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            fileInfo.classList.remove('hidden');
            
            // Change icon to indicate file is selected
            uploadIcon.innerHTML = '<path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />';
            uploadIcon.classList.remove('text-gray-400');
            uploadIcon.classList.add('text-green-500');
            
            // Enable preview button
            previewBtn.disabled = false;
            previewBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            previewBtn.classList.add('hover:bg-indigo-700');
        }
        
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
    });
    </script>
</x-layouts.app>