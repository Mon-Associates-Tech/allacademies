<x-layouts.app title="Preview Questions" action-link-text="" :action_link="''">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Dashboard' => route('dashboard'),
            'Academic Groups' => route('academic-groups.index'),
            $academicTopic->academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
            $academicTopic->academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
            $academicTopic->academicSubject->name => route('academic-subjects.show', ['academic_subject' => $academicTopic->academicSubject, 'academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
            $academicTopic->name => route('academic-topics.show', ['academic_topic' => $academicTopic, 'academic_subject' => $academicTopic->academicSubject, 'academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
            'Preview Questions' => null,
        ]" />
    </x-slot>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
                        Preview Questions for "{{ $academicTopic->name }}"
                        @if($academicSubtopic)
                            <span class="text-sm font-normal text-gray-600 dark:text-gray-400"> / "{{ $academicSubtopic->name }}"</span>
                        @endif
                    </h2>
                </div>

                @if(isset($previewData['errors']) && !empty($previewData['errors']))
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 m-4 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Import Errors</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc pl-5">
                                        @foreach($previewData['errors'] as $error)
                                            <li>{{ $error['message'] }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if(isset($previewData['preview']) && !empty($previewData['preview']))
                    <div class="p-6">
                        <div class="mb-4 flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Questions Preview</h3>
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ count($previewData['preview']) }} questions found</span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Row</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Question</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Options</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Answer</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Difficulty</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Score</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($previewData['preview'] as $item)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $item['row_number'] ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white max-w-xs">
                                                <div class="truncate" title="{{ $item['question'] }}">{{ $item['question'] }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                    {{ ucfirst(str_replace('_', ' ', $item['type'])) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                @if(in_array(strtolower($item['type']), ['multiple_choice', 'mcq', 'multiple choice']))
                                                    <ul class="list-disc pl-5 space-y-1">
                                                        @if(!empty($item['option_a']))<li><strong>A:</strong> {{ $item['option_a'] }}</li>@endif
                                                        @if(!empty($item['option_b']))<li><strong>B:</strong> {{ $item['option_b'] }}</li>@endif
                                                        @if(!empty($item['option_c']))<li><strong>C:</strong> {{ $item['option_c'] }}</li>@endif
                                                        @if(!empty($item['option_d']))<li><strong>D:</strong> {{ $item['option_d'] }}</li>@endif
                                                        @if(!empty($item['option_e']))<li><strong>E:</strong> {{ $item['option_e'] }}</li>@endif
                                                    </ul>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                @if(is_bool($item['answer']))
                                                    {{ $item['answer'] ? 'True' : 'False' }}
                                                @else
                                                    {{ $item['answer'] }}
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @if($item['difficulty_level'] === 'easy') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                    @elseif($item['difficulty_level'] === 'medium') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                                    @endif">
                                                    {{ ucfirst($item['difficulty_level']) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $item['score'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6 flex items-center justify-between">
                            <a href="{{ route('questions.import.form', [
                                'academic_topic' => $academicTopic,
                                'academic_subject' => $academic_subject,
                                'academic_level' => $academic_level,
                                'academic_group' => $academic_group
                            ]) }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                Back to Import
                            </a>
                            
                            <form method="POST" action="{{ route('questions.import', [
                                'academic_topic' => $academicTopic,
                                'academic_subject' => $academic_subject,
                                'academic_level' => $academic_level,
                                'academic_group' => $academic_group
                            ]) }}" class="inline" id="import-form">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-green-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" id="import-button">
                                    Confirm & Import Questions
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No questions found</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            The file does not contain any valid questions to import.
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('questions.import.form', [
                                'academic_topic' => $academicTopic,
                                'academic_subject' => $academic_subject,
                                'academic_level' => $academic_level,
                                'academic_group' => $academic_group
                            ]) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                Back to Import
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const importForm = document.getElementById('import-form');
            const importButton = document.getElementById('import-button');
            let isSubmitting = false;
            
            importForm.addEventListener('submit', function(e) {
                if (isSubmitting) {
                    e.preventDefault();
                    return false;
                }
                
                isSubmitting = true;
                importButton.disabled = true;
                importButton.textContent = 'Importing...';
                
                // Show a loading indicator or message
                importButton.classList.add('opacity-75', 'cursor-not-allowed');
            });
        });
    </script>
</x-layouts.app>