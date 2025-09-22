<x-layouts.app>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Student Details: {{ $student->user->name }}
                </h3>
                <a href="{{ route('admin.student-management') }}" class="text-indigo-600 hover:text-indigo-900">
                    Back to Students
                </a>
            </div>
        </div>

        <div class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Personal Information</h4>
                    <dl class="grid grid-cols-1 gap-2">
                        <div class="flex">
                            <dt class="text-sm font-medium text-gray-500 w-32">Name:</dt>
                            <dd class="text-sm text-gray-900">{{ $student->user->name }}</dd>
                        </div>
                        <div class="flex">
                            <dt class="text-sm font-medium text-gray-500 w-32">Email:</dt>
                            <dd class="text-sm text-gray-900">{{ $student->user->email }}</dd>
                        </div>
                        <div class="flex">
                            <dt class="text-sm font-medium text-gray-500 w-32">Phone:</dt>
                            <dd class="text-sm text-gray-900">{{ $student->user->phone ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex">
                            <dt class="text-sm font-medium text-gray-500 w-32">Date of Birth:</dt>
                            <dd class="text-sm text-gray-900">{{ $student->date_of_birth ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex">
                            <dt class="text-sm font-medium text-gray-500 w-32">Blood Group:</dt>
                            <dd class="text-sm text-gray-900">{{ $student->blood_group ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex">
                            <dt class="text-sm font-medium text-gray-500 w-32">Address:</dt>
                            <dd class="text-sm text-gray-900">{{ $student->address ?? 'N/A' }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-gray-50 p-6 rounded-lg">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Academic Information</h4>
                    <dl class="grid grid-cols-1 gap-2">
                        <div class="flex">
                            <dt class="text-sm font-medium text-gray-500 w-32">Student ID:</dt>
                            <dd class="text-sm text-gray-900">{{ $student->student_id ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex">
                            <dt class="text-sm font-medium text-gray-500 w-32">Academic Level:</dt>
                            <dd class="text-sm text-gray-900">{{ $student->academicLevel->name ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex">
                            <dt class="text-sm font-medium text-gray-500 w-32">Academic Group:</dt>
                            <dd class="text-sm text-gray-900">{{ $student->academicGroup->name ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex">
                            <dt class="text-sm font-medium text-gray-500 w-32">Student Group:</dt>
                            <dd class="text-sm text-gray-900">{{ $student->studentGroup->name ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex">
                            <dt class="text-sm font-medium text-gray-500 w-32">Admission Date:</dt>
                            <dd class="text-sm text-gray-900">{{ $student->admission_date ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex">
                            <dt class="text-sm font-medium text-gray-500 w-32">Status:</dt>
                            <dd class="text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $student->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($student->status) }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="mt-8" x-data="{ activeTab: 'actions' }">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8">
                        <button
                            @click="activeTab = 'actions'"
                            :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'actions', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'actions' }"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Actions
                        </button>
                        <button
                            @click="activeTab = 'progression'"
                            :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'progression', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'progression' }"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Academic Progression
                        </button>
                        <button
                            @click="activeTab = 'report-cards'"
                            :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'report-cards', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'report-cards' }"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Report Cards
                        </button>
                        <button
                            @click="activeTab = 'id-cards'"
                            :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'id-cards', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'id-cards' }"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            ID Cards
                        </button>
                    </nav>
                </div>

                <!-- Actions Tab -->
                <div x-show="activeTab === 'actions'" class="py-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h5 class="text-md font-medium text-gray-900 mb-4">Promote Student</h5>
                            <form action="{{ route('student.promote', $student) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label for="new_academic_level_id" class="block text-sm font-medium text-gray-700 mb-1">
                                        New Academic Level
                                    </label>
                                    <select name="new_academic_level_id" id="new_academic_level_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                        <option value="">Select Academic Level</option>
                                        @foreach(App\Models\AcademicLevel::where('school_id', $student->school_id)->get() as $level)
                                            @if(!$student->academicLevel || $level->id != $student->academicLevel->id)
                                                <option value="{{ $level->id }}">{{ $level->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="promotion_date" class="block text-sm font-medium text-gray-700 mb-1">
                                        Promotion Date
                                    </label>
                                    <input type="date" name="promotion_date" id="promotion_date" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ date('Y-m-d') }}" required>
                                </div>

                                <div class="mb-4">
                                    <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">
                                        Remarks
                                    </label>
                                    <textarea name="remarks" id="remarks" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" rows="3"></textarea>
                                </div>

                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    Promote Student
                                </button>
                            </form>
                        </div>

                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h5 class="text-md font-medium text-gray-900 mb-4">Generate Documents</h5>
                            <div class="space-y-4">
                                <form action="{{ route('student.generate-report-card', $student) }}" method="POST" class="space-y-4">
                                    @csrf
                                    <div>
                                        <label for="academic_year_id" class="block text-sm font-medium text-gray-700 mb-1">
                                            Academic Year
                                        </label>
                                        <select name="academic_year_id" id="academic_year_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                            <option value="">Select Academic Year</option>
                                            <!-- Populate with academic years -->
                                        </select>
                                    </div>

                                    <div>
                                        <label for="term" class="block text-sm font-medium text-gray-700 mb-1">
                                            Term/Semester
                                        </label>
                                        <input type="text" name="term" id="term" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="e.g., Term 1, Semester 1" required>
                                    </div>

                                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V8z" clip-rule="evenodd" />
                                        </svg>
                                        Generate Report Card
                                    </button>
                                </form>

                                <form action="{{ route('student.generate-id-card', $student) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4 5a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V7a2 2 0 00-2-2h-1.586a1 1 0 01-.707-.293l-1.121-1.121A2 2 0 0011.172 3H8.828a2 2 0 00-1.414.586L6.293 4.707A1 1 0 015.586 5H4zm6 9a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                                        </svg>
                                        Generate ID Card
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Academic Progression Tab -->
                <div x-show="activeTab === 'progression'" class="py-6">
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h5 class="text-md font-medium text-gray-900 mb-4">Academic Progression History</h5>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Academic Level</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($student->academicProgression as $progression)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $progression->academicLevel->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $progression->start_date->format('M d, Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $progression->end_date ? $progression->end_date->format('M d, Y') : 'Current' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $progression->status === 'current' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ ucfirst($progression->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $progression->notes ?? 'N/A' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No academic progression records found
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Report Cards Tab -->
                <div x-show="activeTab === 'report-cards'" class="py-6">
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h5 class="text-md font-medium text-gray-900 mb-4">Report Cards</h5>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Academic Year</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Term</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Generated At</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($student->reportCards as $reportCard)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $reportCard->academicYear->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $reportCard->term }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $reportCard->generated_at ? $reportCard->generated_at->format('M d, Y') : 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <a href="{{ route('student.print-report-card', $reportCard) }}" class="text-indigo-600 hover:text-indigo-900">
                                                Print
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No report cards found
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ID Cards Tab -->
                <div x-show="activeTab === 'id-cards'" class="py-6">
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h5 class="text-md font-medium text-gray-900 mb-4">ID Cards</h5>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Card Number</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Issue Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiry Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($student->idCards as $idCard)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $idCard->card_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $idCard->issue_date->format('M d, Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $idCard->expiry_date->format('M d, Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $idCard->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ ucfirst($idCard->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($idCard->status === 'active')
                                            <a href="{{ route('student.print-id-card', $student) }}" class="text-indigo-600 hover:text-indigo-900">
                                                Print
                                            </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No ID cards found
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
    </div>
</div>
</x-layouts.app>
