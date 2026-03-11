<div class="min-h-screen bg-gray-50 dark:bg-gray-900"
     x-data="{
        selectAll: false,
        toggleAll() {
            this.selectAll = !this.selectAll;
            $wire.toggleAllStudents(this.selectAll);
        }
     }">
    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-6">
            <div class="sm:flex sm:items-center sm:justify-between">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Take Attendance</h2>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('teachers.attendance.index') }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600">
                        Back to List
                    </a>
                </div>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="mb-4 rounded-md bg-green-50 dark:bg-green-900 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('message') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form Section -->
        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <div class="p-6">
                <form wire:submit.prevent="saveAttendance">
                    <!-- Session Details -->
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <div class="sm:col-span-2">
                            <label for="academicLevelId" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Academic Level
                            </label>
                            <select wire:model.live="academicLevelId" id="academicLevelId"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Select Level</option>
                                @foreach($academicLevels as $level)
                                    <option value="{{ $level['id'] }}">{{ $level['name'] }}</option>
                                @endforeach
                            </select>
                            @error('academicLevelId') <span class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="academicSubjectId" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Subject (Optional)
                            </label>
                            <select wire:model.live="academicSubjectId" id="academicSubjectId"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">All Subjects</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject['id'] }}">{{ $subject['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sm:col-span-1">
                            <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Date
                            </label>
                            <input type="date" wire:model="date" id="date"
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @error('date') <span class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                        </div>

                        <div class="sm:col-span-1">
                            <label for="session" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Session
                            </label>
                            <select wire:model="session" id="session"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="morning">Morning</option>
                                <option value="afternoon">Afternoon</option>
                                <option value="full_day">Full Day</option>
                            </select>
                        </div>
                    </div>

                    <!-- Students List -->
                    @if(count($allStudents) > 0)
                        <div class="mt-8">
                            <div class="sm:flex sm:items-center sm:justify-between mb-4">
                                <div class="flex-1 sm:flex sm:items-center sm:gap-4">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Students Attendance</h3>
                                    <!-- Search Box -->
                                    <div class="mt-2 sm:mt-0 sm:w-64">
                                        <label for="search" class="sr-only">Search students</label>
                                        <div class="relative rounded-md shadow-sm">
                                            <input type="text"
                                                   wire:model.debounce.300ms="searchQuery"
                                                   class="block w-full rounded-md border-gray-300 pr-10 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                   placeholder="Search students...">
                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3 sm:mt-0">
                                    <button type="button"
                                            x-on:click="toggleAll"
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600">
                                        <span x-text="selectAll ? 'Uncheck All' : 'Check All Present'"></span>
                                    </button>
                                </div>
                            </div>

                            <div class="mt-2 flex flex-col">
                                <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                    <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                                        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                                            <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                                                <thead class="bg-gray-50 dark:bg-gray-700">
                                                <tr>
                                                    <th scope="col" class="relative w-12 px-6 sm:w-16 sm:px-8">
                                                        <input type="checkbox"
                                                               x-model="selectAll"
                                                               class="absolute left-4 top-1/2 -mt-2 h-4 w-4 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-indigo-600 dark:text-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700">
                                                    </th>
                                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Student</th>
                                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Status</th>
                                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Reason for Absence</th>
                                                </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                                @forelse($students as $student)
                                                    <tr wire:key="student-{{ $student['id'] }}">
                                                        <td class="relative w-12 px-6 sm:w-16 sm:px-8">
                                                            <div class="absolute left-4 top-1/2 -mt-2">
                                                                <input type="checkbox"
                                                                       wire:model="selectedStudents.{{ $student['id'] }}.present"
                                                                       x-on:change="$wire.studentPresenceChanged({{ $student['id'] }}, $event.target.checked)"
                                                                       class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-indigo-600 dark:text-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700">
                                                            </div>
                                                        </td>
                                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm">
                                                            <div class="flex items-center">
                                                                <div class="h-10 w-10 flex-shrink-0">
                                                                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-gray-500">
                                                                            <span class="font-medium leading-none text-white">{{ substr($student['name'], 0, 1) }}</span>
                                                                        </span>
                                                                </div>
                                                                <div class="ml-4">
                                                                    <div class="font-medium text-gray-900 dark:text-white">{{ $student['name'] }}</div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                                                <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 {{ isset($selectedStudents[$student['id']]['present']) && $selectedStudents[$student['id']]['present']
                                                                    ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100'
                                                                    : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' }}">
                                                                    {{ isset($selectedStudents[$student['id']]['present']) && $selectedStudents[$student['id']]['present'] ? 'Present' : 'Absent' }}
                                                                </span>
                                                        </td>
                                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                                            @if(!isset($selectedStudents[$student['id']]['present']) || !$selectedStudents[$student['id']]['present'])
                                                                <input type="text"
                                                                       wire:model.defer="selectedStudents.{{ $student['id'] }}.reason"
                                                                       class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                                       placeholder="Enter reason for absence">
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                                            {{ $searchQuery ? 'No students found matching your search.' : 'No students available.' }}
                                                        </td>
                                                    </tr>
                                                @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center mt-6">
                            <p class="text-gray-500 dark:text-gray-400">Please select an academic level to load students.</p>
                        </div>
                    @endif

                    <!-- Submit Button -->
                    <div class="mt-6 flex justify-end">
                        <button type="submit"
                                class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                            Save Attendance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
