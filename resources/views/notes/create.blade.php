<x-layouts.app>
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">
                {{ isset($note) ? 'Edit Note' : 'Create New Note' }}
            </h2>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ isset($note) ? route('notes.update', $note) : route('notes.store') }}">
                @csrf
                @if(isset($note))
                    @method('PUT')
                @endif

                <div class="mb-4">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" name="title" id="title"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           value="{{ old('title', $note->title ?? '') }}" required>
                </div>

                <div class="mb-4">
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                    <textarea name="content" id="content" rows="10"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              required>{{ old('content', $note->content ?? '') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="book_id" class="block text-sm font-medium text-gray-700 mb-1">Related Book (Optional)</label>
                        <select name="book_id" id="book_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select a book</option>
                            @foreach($books as $book)
                                <option value="{{ $book->id }}" {{ (isset($note) && $note->book_id == $book->id) ? 'selected' : '' }}>
                                    {{ $book->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="academic_subject_id" class="block text-sm font-medium text-gray-700 mb-1">Academic Subject (Optional)</label>
                        <select name="academic_subject_id" id="academic_subject_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select a subject</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ (isset($note) && $note->academic_subject_id == $subject->id) ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-6">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_public" id="is_public"
                               class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                               {{ old('is_public', $note->is_public ?? false) ? 'checked' : '' }}>
                        <label for="is_public" class="ml-2 block text-sm text-gray-900">
                            Make Public
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3">
                    <a href="{{ route('notes.index') }}"
                       class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        {{ isset($note) ? 'Update Note' : 'Create Note' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</x-layouts.app>
