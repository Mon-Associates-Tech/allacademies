<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">My Notes</h2>
            <a href="{{ route('notes.create') }}"
               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create New Note
            </a>
        </div>

        <div class="p-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @forelse($notes as $note)
                <div class="border-b border-gray-200 py-4 last:border-0">
                    <h3 class="text-lg font-medium text-gray-900 hover:text-blue-600">
                        <a href="{{ route('notes.show', $note) }}">{{ $note->title }}</a>
                    </h3>
                    <p class="mt-1 text-gray-600">{{ Str::limit($note->content, 150) }}</p>

                    <div class="mt-3 flex justify-between items-center">
                        <p class="text-sm text-gray-500">
                            Created by {{ $note->user->name }}
                            @if($note->book)
                                <span>| Related to book: {{ $note->book->title }}</span>
                            @endif
                            @if($note->academicSubject)
                                <span>| Subject: {{ $note->academicSubject->name }}</span>
                            @endif
                        </p>

                        <div class="flex space-x-2">
                            @if($note->user_id === Auth::id())
                                <a href="{{ route('notes.edit', $note) }}"
                                   class="text-sm bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-1 px-3 rounded">
                                    Edit
                                </a>
                            @elseif($note->canUserEdit(Auth::id()))
                                <a href="{{ route('notes.edit', $note) }}"
                                   class="text-sm bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-1 px-3 rounded">
                                    Edit
                                </a>
                            @endif

                            @if($note->book)
                                <a href="{{ route('books.show', $note->book) }}"
                                   class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-1 px-3 rounded">
                                    Go to Book
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <p class="text-gray-500">You haven't created or been shared any notes yet.</p>
                </div>
            @endforelse

            <div class="mt-6">
                {{ $notes->links() }}
            </div>
        </div>
    </div>
</div>
</x-layouts.app>
