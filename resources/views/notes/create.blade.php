<x-layouts.app>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Single Cohesive Container --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">

            {{-- Header Section --}}
            <div class="page-header-blue py-4">
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('notes.index') }}"
                       class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                       title="Back to Notes">
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ isset($note) ? 'Edit Note' : 'Create New Note' }}
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                            {{ isset($note) ? 'Update your note details and content' : 'Add a new note to your collection' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Form Content --}}
            <form method="POST" action="{{ isset($note) ? route('notes.update', $note) : route('notes.store') }}">
                @csrf
                @if(isset($note))
                    @method('PUT')
                @endif

                <div class="px-6 py-6 sm:px-8 space-y-6">
                    {{-- Title --}}
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="title"
                               id="title"
                               value="{{ old('title', $note->title ?? '') }}"
                               placeholder="Enter a descriptive title for your note..."
                               class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('title') border-red-500 @enderror"
                               required>
                        @error('title')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Content with Markdown Editor --}}
                    <div>
                        <x-form.markdown-editor
                            name="content"
                            label="Content"
                            :value="old('content', $note->content ?? '')"
                            :height="500"
                            info="Use the rich text editor to format your note with headings, lists, bold, italic, and more."
                            required
                        />
                        @error('content')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Book and Subject Selection --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Book Selection --}}
                        <div>
                            <label for="book_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Related Book
                                <span class="text-gray-400 font-normal">(Optional)</span>
                            </label>
                            <div class="relative">
                                <select name="book_id"
                                        id="book_id"
                                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none @error('book_id') border-red-500 @enderror">
                                    <option value="">Select a book</option>
                                    @foreach($books as $book)
                                        <option value="{{ $book->id }}"
                                            {{ (old('book_id', $note->book_id ?? '') == $book->id) ? 'selected' : '' }}>
                                            {{ $book->title }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                            @error('book_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Subject Selection --}}
                        <div>
                            <label for="academic_subject_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Academic Subject
                                <span class="text-gray-400 font-normal">(Optional)</span>
                            </label>
                            <div class="relative">
                                <select name="academic_subject_id"
                                        id="academic_subject_id"
                                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none @error('academic_subject_id') border-red-500 @enderror">
                                    <option value="">Select a subject</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}"
                                            {{ (old('academic_subject_id', $note->academic_subject_id ?? '') == $subject->id) ? 'selected' : '' }}>
                                            {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                            @error('academic_subject_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Visibility Settings --}}
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox"
                                       name="is_public"
                                       id="is_public"
                                       {{ old('is_public', $note->is_public ?? false) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                            </div>
                            <div class="ml-3">
                                <label for="is_public" class="font-medium text-sm text-gray-900 dark:text-gray-100">
                                    Make this note public
                                </label>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Public notes can be viewed by anyone in your school. Private notes are only visible to you and people you share them with.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Help Tips --}}
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                    Tips for creating great notes
                                </h3>
                                <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>Use descriptive titles to help you find notes later</li>
                                        <li>Link notes to books and subjects for better organization</li>
                                        <li>Use headings and lists to structure your content</li>
                                        <li>Make notes public if you want to share knowledge with classmates</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer Actions --}}
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between gap-3">
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        <span class="text-red-500">*</span> Required fields
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('notes.index') }}"
                           class="px-5 py-2.5 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                            Cancel
                        </a>
                        <button type="submit"
                                class="inline-flex items-center px-5 py-2.5 text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors shadow-sm">
                            @if(isset($note))
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Update Note
                            @else
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Create Note
                            @endif
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
