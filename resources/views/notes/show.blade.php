<x-layouts.app>
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">{{ $note->title }}</h2>

            <div class="flex space-x-2">
                @if($note->canUserEdit(Auth::id()))
                    <a href="{{ route('notes.edit', $note) }}"
                       class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Edit
                    </a>
                @endif

                @if($note->book)
                    <a href="{{ route('books.show', $note->book) }}"
                       class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Go to Book
                    </a>
                @endif

                <a href="{{ route('notes.index') }}"
                   class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">
                    Back to Notes
                </a>
            </div>
        </div>

        <div class="p-6">
            <div class="mb-4">
                <p class="text-sm text-gray-500">
                    Created by {{ $note->user->name }} on {{ $note->created_at->format('M d, Y H:i') }}
                    @if($note->book)
                        <span class="block mt-1">Related to book: <strong>{{ $note->book->title }}</strong></span>
                    @endif
                    @if($note->academicSubject)
                        <span class="block">Subject: <strong>{{ $note->academicSubject->name }}</strong></span>
                    @endif
                </p>
            </div>

            <div class="prose max-w-none border-t border-gray-200 pt-4">
                {!! nl2br(e($note->content)) !!}
            </div>

            @if($note->user_id === Auth::id())
                <div class="mt-8 border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Share this note</h3>

                    <form method="POST" action="{{ route('notes.share', $note) }}" class="mb-6">
                        @csrf
                        <div class="flex flex-col sm:flex-row gap-3">
                            <select name="user_id"
                                    class="flex-grow px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required>
                                <option value="">Select user to share with</option>
                                <!-- You would populate this with actual users -->
                            </select>

                            <div class="flex items-center">
                                <input type="checkbox" name="can_edit" id="can_edit"
                                       class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label for="can_edit" class="ml-2 block text-sm text-gray-900">
                                    Allow editing
                                </label>
                            </div>

                            <button type="submit"
                                    class="px-4 py-2 text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Share
                            </button>
                        </div>
                    </form>

                    @if($note->shares->count() > 0)
                        <div>
                            <h4 class="text-md font-medium text-gray-900 mb-2">Currently shared with:</h4>
                            <ul class="border border-gray-200 rounded-md divide-y divide-gray-200">
                                @foreach($note->shares as $share)
                                    <li class="p-3 flex justify-between items-center">
                                        <span class="text-gray-900">{{ $share->sharedWithUser->name }}</span>
                                        <div class="flex items-center space-x-3">
                                            <span class="text-sm text-gray-500">
                                                {{ $share->can_edit ? 'Can edit' : 'View only' }}
                                            </span>
                                            <form method="POST" action="{{ route('notes.unshare', [$note, $share->sharedWithUser]) }}"
                                                  class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="text-red-600 hover:text-red-900 text-sm font-medium">
                                                    Unshare
                                                </button>
                                            </form>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
</x-layouts.app>
