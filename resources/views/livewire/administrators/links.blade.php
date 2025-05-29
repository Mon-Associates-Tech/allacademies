@props(['activeTab'])
<div class="w-64 bg-gray-800 min-h-screen p-4">
    <div class="text-white text-xl font-bold mb-8">All Academies Admin</div>

    <nav>
        <ul>
            <li class="mb-2">
                <button wire:click="setActiveTab('overview')"
                        class="w-full text-left px-4 py-2 rounded {{ $activeTab === 'overview' ? 'bg-indigo-600' : 'hover:bg-gray-700' }} text-white">
                    Dashboard Overview
                </button>
            </li>
            <li class="mb-2">
                <button wire:click="setActiveTab('users')"
                        class="w-full text-left px-4 py-2 rounded {{ $activeTab === 'users' ? 'bg-indigo-600' : 'hover:bg-gray-700' }} text-white">
                    User Management
                </button>
            </li>
            <li class="mb-2">
                <button wire:click="setActiveTab('students')"
                        class="w-full text-left px-4 py-2 rounded {{ $activeTab === 'students' ? 'bg-indigo-600' : 'hover:bg-gray-700' }} text-white">
                    Student Management
                </button>
            </li>
            <li class="mb-2">
                <button wire:click="setActiveTab('groups')"
                        class="w-full text-left px-4 py-2 rounded {{ $activeTab === 'groups' ? 'bg-indigo-600' : 'hover:bg-gray-700' }} text-white">
                    Student Groups
                </button>
            </li>
            <li class="mb-2">
                <button wire:click="setActiveTab('teachers')"
                        class="w-full text-left px-4 py-2 rounded {{ $activeTab === 'teachers' ? 'bg-indigo-600' : 'hover:bg-gray-700' }} text-white">
                    Teacher Management
                </button>
            </li>
            <li class="mb-2">
                <button wire:click="setActiveTab('librarians')"
                        class="w-full text-left px-4 py-2 rounded {{ $activeTab === 'librarians' ? 'bg-indigo-600' : 'hover:bg-gray-700' }} text-white">
                    Librarian Management
                </button>
            </li>
            <li class="mb-2">
                <button wire:click="setActiveTab('authors')"
                        class="w-full text-left px-4 py-2 rounded {{ $activeTab === 'authors' ? 'bg-indigo-600' : 'hover:bg-gray-700' }} text-white">
                    Author Management
                </button>
            </li>
            <li class="mb-2">
                <button wire:click="setActiveTab('books')"
                        class="w-full text-left px-4 py-2 rounded {{ $activeTab === 'books' ? 'bg-indigo-600' : 'hover:bg-gray-700' }} text-white">
                    Book Management
                </button>
            </li>
            <li class="mb-2">
                <button wire:click="setActiveTab('book-approvals')"
                        class="w-full text-left px-4 py-2 rounded {{ $activeTab === 'book-approvals' ? 'bg-indigo-600' : 'hover:bg-gray-700' }} text-white">
                    Book Approvals
                </button>
            </li>
            <li class="mb-2">
                <button wire:click="setActiveTab('subjects')"
                        class="w-full text-left px-4 py-2 rounded {{ $activeTab === 'subjects' ? 'bg-indigo-600' : 'hover:bg-gray-700' }} text-white">
                    Subject Management
                </button>
            </li>
            <li class="mb-2">
                <button wire:click="setActiveTab('reports')"
                        class="w-full text-left px-4 py-2 rounded {{ $activeTab === 'reports' ? 'bg-indigo-600' : 'hover:bg-gray-700' }} text-white">
                    Reports
                </button>
            </li>
        </ul>
    </nav>
</div>

