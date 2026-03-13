<x-layouts.app title="Take Quiz">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(isset($quizData))
                <livewire:shared.quiz-engine :quiz-data="$quizData" />
            @else
                <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow text-center">
                    <p class="text-gray-600 dark:text-gray-400">No quiz data available.</p>
                    <a href="{{ route('quiz.test-create') }}" class="mt-4 inline-block text-indigo-600 hover:text-indigo-500 font-bold">Create a Quiz</a>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
