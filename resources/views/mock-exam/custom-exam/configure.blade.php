<x-layouts.app>
    <div class="relative overflow-hidden" style="border-radius: 2px;">
        <div class="absolute inset-x-0 top-0 h-1 bg-purple-500"></div>
        <div class="bg-gradient-to-r from-[#0c1f3f] via-[#132a52] to-[#0c1f3f] px-6 py-10 sm:px-10">
            <div class="flex items-center gap-3">
                <a href="{{ route('mock-exams.generate.custom.index') }}"
                   class="text-slate-300 hover:text-white transition">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-semibold text-white">Configure Your Exam</h1>
                    <p class="mt-1 text-sm text-slate-300">
                        {{ $academicSubject->name }} — {{ $academicSubject->academicLevel->name }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="py-8">
        @if (session('errors'))
            <div class="mb-6 max-w-4xl mx-auto">
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 dark:bg-red-900/20 dark:border-red-700">
                    @if ($errors->has('questions'))
                        <p class="text-sm text-red-700 dark:text-red-200">{{ $errors->first('questions') }}</p>
                    @endif
                    @if ($errors->has('topics'))
                        <p class="text-sm text-red-700 dark:text-red-200">{{ $errors->first('topics') }}</p>
                    @endif
                    @if ($errors->has('general'))
                        <p class="text-sm text-red-700 dark:text-red-200">{{ $errors->first('general') }}</p>
                    @endif
                </div>
            </div>
        @endif

        <form method="POST"
              action="{{ route('mock-exams.generate.custom.preview', ['academicSubject' => $academicSubject]) }}"
              class="max-w-4xl mx-auto">
            @csrf

            <!-- Hidden metadata field -->
            <input type="hidden" name="metadata"
                   value="{{ base64_encode(json_encode($metadata, JSON_THROW_ON_ERROR)) }}">

            <!-- Heading Component -->
            <div class="mb-8">
                <livewire:mock-exam.custom-exam.custom-exam-heading :metadata="$metadata" />
            </div>

            <!-- Sections Component -->
            <div class="mb-8">
                <livewire:mock-exam.custom-exam.custom-exam-sections :topics="$topics" />
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-center gap-4 mt-8">
                <a href="{{ route('mock-exams.generate.custom.configure', ['academicSubject' => $academicSubject]) }}"
                   class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition"
                   style="border-radius: 2px;">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2 text-sm font-medium text-white bg-[#0c1f3f] rounded-lg hover:bg-purple-700 transition dark:bg-purple-600 dark:hover:bg-purple-500"
                        style="border-radius: 2px;">
                    Generate Preview
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
